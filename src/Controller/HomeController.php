<?php

namespace App\Controller;

use App\Entity\Shops;
use App\Entity\Towns;
use App\Entity\TownSearch;
use App\Form\TownSearchType;
use App\Repository\ShopCategoriesRepository;
use App\Repository\ShopsRepository;
use App\Repository\TownsRepository;
use App\Service\ToolsShopService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// ***********************************************************************************************
// **                                      INDEX                                                **
// **                                                                                           **
// **                             @Route("/", name="home")                                      **
// **                    Route("/consult-shop/{id}", name="enter-shop")                         **
// **                                                                                           **
// ***********************************************************************************************

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     *
     * @var Towns[] $towns
     * @var Towns $town
     * @param Request $request
     * @param TownsRepository $townsRepository
     * @param ShopsRepository $shopsRepository
     * @param ShopCategoriesRepository $shopCategoriesRepository
     * @return Response
     */
    public function index(ToolsShopService $shopService,Request $request, ShopCategoriesRepository $shopCategoriesRepository): Response
    {
        $search = new TownSearch();
        $message = false;
        $form = $this->createForm(TownSearchType::class, $search);
        $form->handleRequest($request);

        $shopList = $shopService->searchOrNotToSearch($search);
        if (count($shopList) == 0) {
            $message = true;
        }
        return $this->render('home/index.html.twig', [
            'categories' => $shopCategoriesRepository->findAll(),
            'current_menu' => 'home',
            'shopList' => $shopList,
            'formSearchTown' => $form->createView(),
            'message' => $message,
            'search' => $search->getZipCodeSearch() || $search->getNameTownSearch()
        ]);
    }

    /**
     * 
     * login or not login enter in to the shop
     *@Route("/consult-shop/{id}", name="enter-shop")
     * 
     * @return Response
     */
    public function userInsideShop(ToolsShopService $shopService,Shops $shop){
        
        return $this->render('shop/userViewInsideShop.html.twig', [
            'controller_name' => 'home',
            'current_menu' => 'userInsideShop',
            'current_shop' => $shopService->getCurrentShop(),
            'sub_categories' => $shopService->getSubCategories(),
            'nbSubCat' => $shopService->getNumberOfCategories(),
            'products' => $shopService->createPaginationList(),
            'products_without_cat' => $shopService->productWithoutSubCategory()
        ]);
    }
    
}
