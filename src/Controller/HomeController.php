<?php

namespace App\Controller;

use App\Repository\ShopCategoriesRepository;
use App\Repository\ShopsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ShopsRepository $shopsRepository ,ShopCategoriesRepository $shopCategoriesRepository): Response
    {
        $shopList = $shopsRepository->findAll();
        $tabRandomShopList = array_rand($shopList,12);
        $shopRandomList = [];
        foreach($tabRandomShopList as $idRandom){
             array_push($shopRandomList,$shopList[$idRandom]);
        }
        return $this->render('home/index.html.twig', [
            'categories' => $shopCategoriesRepository->findAll(),
            'current_menu' => 'home',
            'shopList' => $shopRandomList
        ]);
    }
}
