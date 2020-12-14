<?php

namespace App\Controller;

use App\Entity\Shops;
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
            'current_user' => $this->getUser(),
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
        // if there are not subCategories 
        if($nbSubCat == 0){
            $products = $paginator->paginate(
                $productRepo->findBy(['shop' => $shop]),
                $request->query->getInt('page',1),
                1
            );   
        } else {
            // pagination of products by category
            $productsBySubCatList = [];
            foreach ($subCategories as $subCategory) {
                $productsBySubCat = $productRepo->getProductsByShopBySubCat($shop, $subCategory);
                $pagination = $paginator->paginate(
                    $productsBySubCat,
                    $request->query->getInt('page', 1),
                    2,
                );
                array_push($productsBySubCatList, $pagination);
            }
            // pagination by category of previous pagination
            $products = $paginator->paginate(
                $productsBySubCatList,
                $request->query->getInt('cat', 1),
                1,
                ['pageParameterName' => 'cat']
            );
        }
        

        return $this->render('shop/manage-shop.html.twig', [
            'controller_name' => 'ShopAdmin',
            'current_menu' => 'manageshop',
            'current_user' => $this->getUser(),
            'current_shop' => $shop,
            'sub_categories' => $subCategories,
            'nbSubCat' => $nbSubCat,
            'products' => $products
        ]);
    }
}
