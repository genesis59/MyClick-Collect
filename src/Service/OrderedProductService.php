<?php

namespace App\Service;

use App\Entity\Ordered;
use App\Entity\OrderedProducts;
use App\Entity\Products;
use App\Entity\Shops;
use App\Entity\User;
use App\Repository\OrderedProductsRepository;
use App\Repository\OrderedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;


class OrderedProductService
{

    /**
     * @var OrderedProductsRepository
     */
    private $orderedProductRepo;

    /**
     * @var OrderedRepository
     */
    private $orderedRepo;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    public function __construct(OrderedProductsRepository $orderedProductRepo, OrderedRepository $orderedRepo, EntityManagerInterface $manager, RequestStack $request)
    {
        $this->orderedProductRepo = $orderedProductRepo;
        $this->orderedRepo = $orderedRepo;
        $this->manager = $manager;
        $this->request = $request->getCurrentRequest();
    }


    /**
     * count of product all state in cart
     *
     * @param User $user
     * @return void
     */
    public function getNbTotalProductInCart(User $user)
    {
        $orderedList = $this->orderedRepo->findBy(['user' => $user]);
        $nbToTalProductInCart = 0;
        foreach ($orderedList as $ordered) {
            $productListByOrdered = $this->orderedProductRepo->findBy(['ordered' => $ordered]);
            $nbToTalProductInCart += count($productListByOrdered);
        }
        return $nbToTalProductInCart;
    }

    /**
     * count of product no validate in cart
     *
     * @param User $user
     * @return void
     */
    public function getNbTotalProductInCartNoValidate(User $user)
    {
        $orderedList = $this->orderedRepo->findBy(['user' => $user, 'status' => 0]);
        $nbToTalProductInCart = 0;
        foreach ($orderedList as $ordered) {
            $productListByOrdered = $this->orderedProductRepo->findBy(['ordered' => $ordered]);
            $nbToTalProductInCart += count($productListByOrdered);
        }
        return $nbToTalProductInCart;
    }

    /**
     * get list of products from ordered list
     *
     * @param Ordered[] $orderedList
     * @return void
     */
    private function getOrderedProductByOrdered($orderedList)
    {
        $productListByOrdered = [];
        foreach ($orderedList as $ordered) {
            $productList = $this->orderedProductRepo->findBy(['ordered' => $ordered]);
            array_push($productListByOrdered, $productList);
        }
        return $productListByOrdered;
    }

    /**
     * get list of products from all ordered list of this user
     *
     * @param User $user
     * @return void
     */
    public function getListProductByAllOrdered(User $user)
    {

        $orderedList = $this->orderedRepo->findBy(['user' => $user]);
        return $this->getOrderedProductByOrdered($orderedList);
    }

    /**
     * get list of products from no validate ordered list of this user
     *
     * @param User $user
     * @return void
     */
    public function getListProductByOrderedNoValidate(User $user)
    {

        $orderedList = $this->orderedRepo->findBy(['user' => $user, 'status' => 0]);
        return $this->getOrderedProductByOrdered($orderedList);
    }

    /**
     * get list of products from validate ordered list of this user
     *
     * @param User $user
     * @return void
     */
    public function getListProductByOrderedValidate(User $user)
    {

        $orderedList = $this->orderedRepo->findBy(['user' => $user, 'status' => 1, 'orderReady' => 0]);
        return $this->getOrderedProductByOrdered($orderedList);
    }

    /**
     * get list of products from ready ordered list of this user
     *
     * @param User $user
     * @return void
     */
    public function getListProductByOrderedReady(User $user)
    {

        $orderedList = $this->orderedRepo->findBy(['user' => $user, 'orderReady' => 1]);
        return $this->getOrderedProductByOrdered($orderedList);
    }


    /**
     * manage cart if it is necessary to create new ordered at the shop or create a new one
     *
     * @param User $user
     * @param Shops $shop
     * @param Products $product
     * @return void
     */
    public function manageCartOrdered(User $user, Shops $shop, Products $product)
    {
        $orderedList = $this->orderedRepo->getOrderedByShopAndUser($shop, $user);
        // if list without order we create it
        if (!$orderedList) {
            $ordered = $this->createNewOrderedAndPersist($user, $shop);
            $this->createNewOrderedProductAndPersist($ordered, $product);
        } else {
            // we suppose all ordered are validate
            $orderedAllValidate = true;
            foreach ($orderedList as $ordered) {
                // if ordered not validate
                if (!$ordered->getStatus()) {
                    $orderedAllValidate = false;
                    // check if product is already in order and persist
                    $this->checkProductIsInListAndPersist($product, $ordered);
                }
            }
            if ($orderedAllValidate) {
                $ordered = $this->createNewOrderedAndPersist($user, $shop);
                $this->createNewOrderedProductAndPersist($ordered, $product);
            }
        }
    }

    /**
     * update status target ordered to validate (status=1)
     *
     * @param Ordered $ordered
     * @return void
     */
    public function validateOneOrderedOfCart(Ordered $ordered)
    {
        foreach ($ordered->getOrderedProducts() as $orderedProduct) {
            $product = $orderedProduct->getProduct();

            if ($orderedProduct->getQuantity() > $product->getStock() || $product->getStock() == 0) {
                $this->request->getSession()->getFlashBag()->add(
                    'notice',
                    'Modifier et/ou supprimer les articles avec des quantités érronés'
                );
            } else {
                $ordered->setStatus(1);
                $product->setStock($product->getStock() - $orderedProduct->getQuantity());
                $this->manager->persist($product);
                $this->manager->flush();
            }
        }
    }

    /**
     * in cart delete product and delete ordered if there aren't product in order
     *
     * @param OrderedProducts $op
     * @return void
     */
    public function manageDeleteOfCartOrdered(OrderedProducts $op)
    {
        if (count($op->getOrdered()->getOrderedProducts()) == 1) {
            $this->manager->remove($op);
            $this->manager->remove($op->getOrdered());
        } else {
            $this->manager->remove($op);
        }
        $this->manager->flush();
    }

    /**
     * manage update quantity for not add a new product if already exist in this ordered
     *
     * @param Products $product
     * @param Ordered $ordered
     * @return void
     */
    private function checkProductIsInListAndPersist(Products $product, Ordered $ordered)
    {
        // check if product is already in order
        if (!$this->orderedProductRepo->getOrderedProductByOrderedAndProduct($product, $ordered)) {
            $orderedProduct = new OrderedProducts();
            $orderedProduct->setOrdered($ordered)
                ->setProduct($product)
                ->setQuantity($this->request->query->get('quantity'));
            $this->manager->persist($orderedProduct);
        } else {
            $orderedProduct = $this->orderedProductRepo->getOrderedProductByOrderedAndProduct($product, $ordered)[0];
            $orderedProduct->setQuantity($this->request->query->get('quantity'));
        }
    }

    /**
     * create a new ordered
     *
     * @param User $user
     * @param Shops $shop
     * @return Ordered
     */
    private function createNewOrderedAndPersist(User $user, Shops $shop)
    {
        $ordered = new Ordered();
        $ordered->setShop($shop)
            ->setUser($user)
            ->setDate(new \DateTime())
            ->setStatus(0)
            ->setOrderReady(0)
            ->setRecupByUser(0);
        $this->manager->persist($ordered);
        return $ordered;
    }

    /**
     * create a new OrderedProduct
     *
     * @param Ordered $ordered
     * @param Products $product
     * @return OrderedProducts
     */
    private function createNewOrderedProductAndPersist(Ordered $ordered, Products $product)
    {
        $orderedProduct = new OrderedProducts();
        $orderedProduct->setOrdered($ordered)
            ->setProduct($product)
            ->setQuantity($this->request->query->get('quantity'));
        $this->manager->persist($orderedProduct);
        return $orderedProduct;
    }
}
