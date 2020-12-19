<?php

namespace App\Controller;

use App\Entity\Ordered;
use App\Entity\OrderedProducts;
use App\Entity\Products;
use App\Entity\Shops;
use App\Entity\User;
use App\Form\OrderedProductsType;
use App\Repository\OrderedProductsRepository;
use App\Repository\OrderedRepository;
use App\Service\OrderedProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderedController extends AbstractController
{
    /**
     * @Route("/ordered", name="ordered")
     */
    public function index(OrderedProductService $ops): Response
    {

        if($this->getUser()){
            $productListByOrdered = $ops->getListProductByOrdered($this->getUser());
            return $this->render('ordered/index.html.twig', [
                'controller_name' => 'OrderedController',
                'product_list_by_ordered' => $productListByOrdered
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
        
        
    }

    /**
     * @Route("/ordered/add-cart/{shop}/{user}/{product}", name="add-cart")
     *
     * @param Request $request
     * @param Shops $shop
     * @param Products $product
     * @param OrderedRepository $orderedRepo
     * @param User $user
     * @return void
     */
    public function addToCart(Request $request, Shops $shop, Products $product, OrderedRepository $orderedRepo, User $user, EntityManagerInterface $manager, OrderedProductsRepository $orderedProductRepo)
    {
        if (!$orderedRepo->getOrderedByShopAndUser($shop, $user)) {
            $ordered = new Ordered();
            $ordered->setShop($shop)
                ->setUser($user)
                ->setDate(new \DateTime())
                ->setStatus(0)
                ->setOrderReady(0);
            $manager->persist($ordered);
            $manager->flush();
        }
        $ordered = $orderedRepo->getOrderedByShopAndUser($shop, $user);
        if (!$orderedProductRepo->getOrderedProductByOrderedAndProduct($product, $ordered[0])) {
            $orderedProduct = new OrderedProducts();
            $orderedProduct->setOrdered($ordered[0])
                ->setProduct($product)
                ->setQuantity($request->query->get('quantity'));
        } else {
            $orderedProduct = $orderedProductRepo->getOrderedProductByOrderedAndProduct($product, $ordered[0])[0];
            $orderedProduct->setQuantity($request->query->get('quantity'));
        }

        $manager->persist($orderedProduct);
        $manager->flush();
        //dd($request);
        return $this->redirect($request->headers->get('referer'));

    }
}
