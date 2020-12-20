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
     * @Route("/ordered", name="ordered-by-home")
     * @Route("/ordered/{id}", name="ordered")
     */
    public function index(OrderedProductService $ops,Shops $shop = null): Response
    {
        if(!$shop) $shop = null;
        if ($this->getUser()) {
            $productListByOrdered = $ops->getListProductByOrdered($this->getUser());
            return $this->render('ordered/index.html.twig', [
                'controller_name' => 'OrderedController',
                'product_list_by_ordered' => $productListByOrdered,
                'last_shop_consult' => $shop
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
    public function addToCart(OrderedProductService $ops, Request $request, Shops $shop, Products $product, User $user, EntityManagerInterface $manager)
    {

        $ops->manageCartOrdered($user, $shop, $product);
        $manager->flush();
        //dd($request);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/ordered/delete-ordered-product/{id}", name="delete-ordered-product")
     *
     * @param OrderedProducts $orderedProduct
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return void
     */
    public function deleteOrderedProduct(OrderedProducts $op, OrderedProductService $ops, Request $request)
    {
        $ops->manageDeleteOfCartOrdered($op);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/ordered/valid-cart/{id}", name="valid-cart")
     *
     * @return void
     */
    public function validCart(Ordered $ordered, OrderedProductService $ops,Request $request)
    {
        $ops->validateOneOrderedOfCart($ordered);
        return $this->redirect($request->headers->get('referer'));
    }
}
