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
    public function addEditShop(Request $request, EntityManagerInterface $manager, Shops $shop = null)
    {
        if (!$shop) {
            $shop = new Shops();
            $shop->setTrader($this->getUser());
            $current_menu = 'new-shop';
        } else {
            $current_menu = 'edit-shop';

        }
        $formShop = $this->createForm(ShopType::class, $shop);
        $formShop->handleRequest($request);
        dump($shop->getPicture());

        if ($formShop->isSubmitted() && $formShop->isValid()) {
            if ($file = $formShop->get('picture')->getData()) {
                if ($shop->getPicture()) {
                    $pastPicture = $this->getParameter('upload_directory') . '/' . $shop->getPicture();
                    if(file_exists($pastPicture)){
                        unlink($pastPicture);
                    }
                }

                $file = $formShop->get('picture')->getData();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_directory'), $fileName);
                $shop->setPicture($fileName);
            }
            $manager->persist($shop);
            $manager->flush();
            return $this->redirectToRoute('shop_admin');
        }
        return $this->render('shop/addshop.html.twig', [
            'controller_name' => 'ShopController',
            'current_menu' => $current_menu,
            'current_user' => $this->getUser(),
            'formNewShop' => $formShop->createView(),
        ]);
    }
}
