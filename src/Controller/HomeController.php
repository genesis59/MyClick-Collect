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
    public function index(ToolsShopService $shopService, Request $request, ShopCategoriesRepository $shopCategoriesRepository, OrderedProductService $ops): Response
    {
        $user = $this->getUser();
        $nbToTalProductInCart = 0;
        $nbToTalProductValidate = 0;
        $nbToTalProductReady = 0;
        $nbToTalProductDelivered = 0;
        if ($user) {
            $nbToTalProductInCart = $ops->getNbTotalProductInProgressByField('user', $user);
            dump($nbToTalProductInCart);
            $nbToTalProductValidate = $ops->getNbTotalOrderedInProgressByField('user', $user, true);
            $nbToTalProductReady = $ops->getNbTotalOrderedInProgressByField('user', $user, true, true);
            $nbToTalProductDelivered = $ops->getNbTotalOrderedInProgressByField('user', $user, true, true, true);
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
            'nb_product_in_cart' => $nbToTalProductInCart,
            'nb_order_validate' => $nbToTalProductValidate,
            'nb_order_ready' => $nbToTalProductReady,
            'nb_order_delivered' => $nbToTalProductDelivered
        ]);
    }

    /**
     * 
     * login or not login enter in to the shop
     *@Route("/consult-shop/{id}", name="enter-shop")
     * 
     * @return Response
     */
    public function userInsideShop(ToolsShopService $shopService, Shops $shop, OrderedProductService $ops)
    {
        $user = $this->getUser();
        $nbToTalProductInCart = 0;
        $nbToTalProductValidate = 0;
        $nbToTalProductReady = 0;
        $nbToTalProductDelivered = 0;
        if ($user) {
            $nbToTalProductInCart = $ops->getNbTotalProductInProgressByField('user', $user);
            dump($nbToTalProductInCart);

            $nbToTalProductValidate = $ops->getNbTotalOrderedInProgressByField('user', $user, true);
            $nbToTalProductReady = $ops->getNbTotalOrderedInProgressByField('user', $user, true, true);
            $nbToTalProductDelivered = $ops->getNbTotalOrderedInProgressByField('user', $user, true, true, true);
        }

        return $this->render('user/userViewInsideShop.html.twig', [
            'controller_name' => 'home',
            'current_menu' => 'userInsideShop',
            'current_shop' => $shopService->getCurrentShop(),
            'sub_categories' => $shopService->getSubCategories(),
            'nbSubCat' => $shopService->getNumberOfCategories(),
            'products' => $shopService->createPaginationList(),
            'products_without_cat' => $shopService->productWithoutSubCategory(),
            'nb_product_in_cart' => $nbToTalProductInCart,
            'nb_order_validate' => $nbToTalProductValidate,
            'nb_order_ready' => $nbToTalProductReady,
            'nb_order_delivered' => $nbToTalProductDelivered
        ]);
    }
}
