<?php

namespace App\Controller;

use App\Entity\Shops;
use App\Entity\ShopSubCategories;
use App\Form\ShopSubCategoriesType;
use App\Form\ShopType;
use App\Repository\ProductsRepository;
use App\Repository\ShopsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * shops admin home
     * @Route("/admin", name="admin")
     */
    public function index(Request $request, ShopsRepository $shopsRepository, PaginatorInterface $paginator): Response
    {
        $shops = $shopsRepository->findShopByUser($this->getUser());
        $shopList = $paginator->paginate(
            $shops,
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopAdmin',
            'current_menu' => 'shop',
            'current_user' => $this->getUser(),
            'shopList' => $shopList,
            'message' => false
        ]);
    }

    /**
     * create or edit shop
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

        if ($formShop->isSubmitted() && $formShop->isValid()) {
            if ($file = $formShop->get('picture')->getData()) {
                if ($shop->getPicture()) {
                    $pastPicture = $this->getParameter('upload_directory') . '/' . $shop->getPicture();
                    if (file_exists($pastPicture)) {
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
            'controller_name' => 'ShopAdmin',
            'current_menu' => $current_menu,
            'formNewShop' => $formShop->createView()
        ]);
    }

    /**
     * shop manager
     * @Route("/manage/{id}",name="manage-shop")
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ProductsRepository $productRepo
     * @param Shops $shop
     * @return void
     */
    public function manageShop(Request $request, PaginatorInterface $paginator, ProductsRepository $productRepo, Shops $shop)
    {


        $subCategories = $shop->getShopSubCategories();
        $nbSubCat = count($subCategories);
        // by default we consider that if there is at least one subcategory all the products have one
        $categoryNotDefined = null;

        // if there are not subCategories we collect all the products from $shop
        if ($nbSubCat == 0) {
            $products = $this->pagination($paginator, $request, $productRepo->getProductsByShopBySubCat($shop), 5);
            dump($products);
        } else {
            //we check if there are products without category
            $categoryNotDefined = $productRepo->getProductsByShopBySubCat($shop);
            // pagination of products by category
            $productsBySubCatList = [];
            foreach ($subCategories as $subCategory) {
                $pagination = $this->pagination($paginator, $request, $productRepo->getProductsByShopBySubCat($shop, $subCategory), 5);
                array_push($productsBySubCatList, $pagination);
            }
            // if product without category
            if ($categoryNotDefined) {
                $pagination = $this->pagination($paginator, $request, $categoryNotDefined, 5);
                array_push($productsBySubCatList, $pagination);
            }
            // pagination by category of previous pagination products
            $products = $this->pagination($paginator, $request, $productsBySubCatList, 1, 'cat');
        }
        return $this->render('shop/manage-shop.html.twig', [
            'controller_name' => 'ShopAdmin',
            'current_menu' => 'manageshop',
            'current_user' => $this->getUser(),
            'current_shop' => $shop,
            'sub_categories' => $subCategories,
            'nbSubCat' => $nbSubCat,
            'products' => $products,
            'not_cat_products' => $categoryNotDefined
        ]);
    }

    /*********************************** SUBCATEGORIES MANAGER ****************************/


    /**
     * list and manage shop sub categories
     * @Route("/manage/sub-categories/{id}",name = "list-sub-categories")
     *
     * @param Request $request
     * @param Shops $shop
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function addAndListSubCategories(Request $request, Shops $shop, EntityManagerInterface $manager)
    {

        $subCategoriesList = $shop->getShopSubCategories();
        $newSubCategory = new ShopSubCategories();
        $form = $this->createForm(ShopSubCategoriesType::class, $newSubCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newSubCategory->setShop($shop);
            $manager->persist($newSubCategory);
            $manager->flush();
            return $this->redirectToRoute('shop_list-sub-categories', ['id' => $shop->getId()]);
        }

        return $this->render('shop/list-sub-categories.html.twig', [
            'controller_name' => 'ShopAdmin',
            'current_menu' => 'list-sub-categories',
            'current_shop' => $shop,
            'subCategories' => $subCategoriesList,
            'form_sub_category' => $form->createView()
        ]);
    }

    /**
     * edit a subCategory
     * @Route("/manage/sub-categories/edit/{id}",name = "edit-sub-category")
     *
     * @param ShopSubCategories $subCategory
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return void
     */
    public function editSubCategory(ShopSubCategories $subCategory,EntityManagerInterface $manager,Request $request){

        $form = $this->createForm(ShopSubCategoriesType::class, $subCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($subCategory);
            $manager->flush();
            return $this->redirectToRoute('shop_list-sub-categories', ['id' => $subCategory->getShop()->getId()]);
        }

        return $this->render('shop/editSubCategory.html.twig',[
            'controller_name' => 'ShopAdmin',
            'current_menu' => 'list-sub-categories',
            'form_sub_category' => $form->createView()
        ]);

    }

    /**
     * delete subCategory
     * @Route("/manage/sub-categories/delete/{id}",name = "delete-sub-category")
     *
     * @param Request $request
     * @param ShopSubCategories $subCategory
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function deleteSubCategoriy(Request $request, ShopSubCategories $subCategory, EntityManagerInterface $manager)
    {
        $productsOfThisSubCategory = $subCategory->getProducts();
        foreach($productsOfThisSubCategory as $product){
            $product->setSubCategory(null);
        }
        $manager->remove($subCategory);
        
        $manager->flush();
        return $this->redirectToRoute('shop_list-sub-categories',['id' => $subCategory->getShop()->getId() ]);
    }

    /*********************************** FONCTIONS ****************************/
    public function pagination(PaginatorInterface $paginator, Request $request, $listToPaginate, Int $nbElementByPage = 5, String $newValueGet = null)
    {
        if ($newValueGet) {
            return $paginator->paginate(
                $listToPaginate,
                $request->query->getInt($newValueGet, 1),
                $nbElementByPage,
                ['pageParameterName' => $newValueGet]
            );
        } else {
            return $paginator->paginate(
                $listToPaginate,
                $request->query->getInt('page', 1),
                $nbElementByPage,
            );
        }
    }
}
