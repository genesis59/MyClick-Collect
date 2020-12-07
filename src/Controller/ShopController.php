<?php

namespace App\Controller;

use App\Entity\Shops;
use App\Form\ShopType;
use App\Repository\ShopsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shop", name="shop_")
 */
class ShopController extends AbstractController
{
    /**
     * SHOP HOME
     * @Route("/admin", name="admin")
     */
    public function index(ShopsRepository $shopsRepository): Response
    {
        $shopList = $shopsRepository->findShopByUser($this->getUser());
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'current_menu' => 'shop',
            'current_user' => $this->getUser(),
            'shopList' => $shopList
        ]);
    }  

    /**
     * CREATE NEW OR EDIT SHOP 
     * @Route("/new-shop", name="new-shop")
     * @Route("/edit-shop/{id}", name="edit-shop")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Shops $shop
     * @return void
     */
    public function addEditShop(Request $request, EntityManagerInterface $manager, Shops $shop = null){
        if(!$shop){
            $shop = new Shops();
            $shop->setTrader($this->getUser());
        }
        $formShop = $this->createForm(ShopType::class,$shop);
        $formShop->handleRequest($request);
        if($formShop->isSubmitted() && $formShop->isValid()){
            $manager->persist($shop);
            $manager->flush();
            return $this->redirectToRoute('shop_admin');
        }
        return $this->render('shop/addshop.html.twig', [
            'controller_name' => 'ShopController',
            'current_menu' => 'shop',
            'current_user' => $this->getUser(),
            'formNewShop' => $formShop->createView(),
        ]);
    }
}
