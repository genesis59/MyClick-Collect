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
        // recupération de la liste des magasins
        $shopList = $shopsRepository->findAll();
        // récupération de 12 clés aléatoire des données de $shopList
        $tabRandomShopList = array_rand($shopList,12);
        // Initialisation d'un tableau pour récupérer les objets Shops
        $shopRandomList = [];
        foreach($tabRandomShopList as $idRandom){
            //Insertion des objets Shops à l'aide des clés aléatoire récupérer au préalable
             array_push($shopRandomList,$shopList[$idRandom]);
        }
        return $this->render('home/index.html.twig', [
            'categories' => $shopCategoriesRepository->findAll(),
            'current_menu' => 'home',
            'shopList' => $shopRandomList
        ]);
    }
}
