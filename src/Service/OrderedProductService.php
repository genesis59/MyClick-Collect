<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\OrderedProductsRepository;
use App\Repository\OrderedRepository;

// ***********************************************************************************************
// **                                      INDEX                                                **
// **                                                                                           **
// **                   function getNbTotalProductInCart(User $user)                            **
// **                          function getListProductByOrdered()                               **
// **                                                                                           **
// **                                                                                           **
// ***********************************************************************************************

class OrderedProductService
{


    private $orderedProductRepo;

    private $orderedRepo;

    public function __construct(OrderedProductsRepository $orderedProductRepo, OrderedRepository $orderedRepo)
    {
        $this->orderedProductRepo = $orderedProductRepo;
        $this->orderedRepo = $orderedRepo;
    }


    public function getNbTotalProductInCart(User $user)
    {
        $orderedList = $this->orderedRepo->findBy(['user' => $user]);
        $nbToTalProductInCart = 0;
        foreach ($orderedList as $ordered) {
            $productListByOrdered = $this->orderedProductRepo->findBy(['ordered' => $ordered]);
            $nbToTalProductInCart += count($productListByOrdered);
        }
        return $nbToTalProductInCart;
    }

    public function getListProductByOrdered(User $user){

        $orderedList = $this->orderedRepo->findBy(['user' => $user]);
        $productListByOrdered = [];
        foreach ($orderedList as $ordered) {
            $productList = $this->orderedProductRepo->findBy(['ordered' => $ordered]);
            array_push($productListByOrdered,$productList);
        }
        return $productListByOrdered;
    }
}
