<?php

namespace App\Controller;

use App\Entity\OrderedProducts;
use App\Entity\Shops;
use App\Entity\Towns;
use App\Entity\TownSearch;
use App\Form\OrderedProductsType;
use App\Form\TownSearchType;
use App\Repository\OrderedProductsRepository;
use App\Repository\OrderedRepository;
use App\Repository\ShopCategoriesRepository;
use App\Repository\ShopsRepository;
use App\Repository\TownsRepository;
use App\Service\OrderedProductService;
use App\Service\ToolsShopService;
use ContainerQQhncSe\getOrderedProductsTypeService;
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
    public function index(ToolsShopService $shopService,Request $request, ShopCategoriesRepository $shopCategoriesRepository,OrderedProductService $or): Response
    {
        $user = $this->getUser();
        $nbToTalProductInCart = 0;
        if($user){
            $nbToTalProductInCart = $or->getNbTotalProductInCartNoValidate($user);
        }
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
            'search' => $search->getZipCodeSearch() || $search->getNameTownSearch(),
            'nb_product_in_cart' => $nbToTalProductInCart

        ]);
    }

    /**
     * 
     * login or not login enter in to the shop
     *@Route("/consult-shop/{id}", name="enter-shop")
     * 
     * @return Response
     */
    public function userInsideShop(ToolsShopService $shopService,Shops $shop,OrderedProductService $or){
        $user = $this->getUser();
        $nbToTalProductInCart = 0;
        if($user){
            $nbToTalProductInCart = $or->getNbTotalProductInCartNoValidate($user);
        }
        
        return $this->render('shop/userViewInsideShop.html.twig', [
            'controller_name' => 'home',
            'current_menu' => 'userInsideShop',
            'current_shop' => $shopService->getCurrentShop(),
            'sub_categories' => $shopService->getSubCategories(),
            'nbSubCat' => $shopService->getNumberOfCategories(),
            'products' => $shopService->createPaginationList(),
            'products_without_cat' => $shopService->productWithoutSubCategory(),
            'nb_product_in_cart' => $nbToTalProductInCart
        ]);
    }
    
}
