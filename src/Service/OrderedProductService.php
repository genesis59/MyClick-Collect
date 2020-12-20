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

// ***********************************************************************************************
// **                                      INDEX                                                **
// **                                                                                           **
// **                   function getNbTotalProductInCart(User $user)                            **
// **                          function getListProductByOrdered()                               **
// **          function manageCartOrdered(User $user, Shops $shop, Products $product)           **
// **                  function validateOneOrderedOfCart(Ordered $ordered)                      **
// **               function manageDeleteOfCartOrdered(OrderedProducts $op)                     **
// **    function checkProductIsInListAndPersist(Products $product,Ordered $ordered)            **
// **                             function createNewOrdered()                                   **
// **                          function createNewOrderedProduct()                               **
// **                                                                                           **
// ***********************************************************************************************

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

    public function __construct(OrderedProductsRepository $orderedProductRepo, OrderedRepository $orderedRepo, EntityManagerInterface $manager,RequestStack $request)
    {
        $this->orderedProductRepo = $orderedProductRepo;
        $this->orderedRepo = $orderedRepo;
        $this->manager = $manager;
        $this->request = $request->getCurrentRequest();
    }


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

    public function getNbTotalProductInCartNoValidate(User $user)
    {
        $orderedList = $this->orderedRepo->findBy(['user' => $user,'status' => 0]);
        $nbToTalProductInCart = 0;
        foreach ($orderedList as $ordered) {
            $productListByOrdered = $this->orderedProductRepo->findBy(['ordered' => $ordered]);
            $nbToTalProductInCart += count($productListByOrdered);
        }
        return $nbToTalProductInCart;
    }

    public function getListProductByOrdered(User $user)
    {

        $orderedList = $this->orderedRepo->findBy(['user' => $user]);
        $productListByOrdered = [];
        foreach ($orderedList as $ordered) {
            $productList = $this->orderedProductRepo->findBy(['ordered' => $ordered]);
            array_push($productListByOrdered, $productList);
        }
        return $productListByOrdered;
    }

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

    private function createNewOrderedAndPersist(User $user, Shops $shop)
    {
        $ordered = new Ordered();
        $ordered->setShop($shop)
            ->setUser($user)
            ->setDate(new \DateTime())
            ->setStatus(0)
            ->setOrderReady(0);
        $this->manager->persist($ordered);
        return $ordered;
    }

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
