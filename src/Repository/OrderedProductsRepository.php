<?php

namespace App\Repository;

use App\Entity\Ordered;
use App\Entity\OrderedProducts;
use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderedProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderedProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderedProducts[]    findAll()
 * @method OrderedProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderedProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderedProducts::class);
    }

    public function getOrderedProductByOrderedAndProduct(Products $product,Ordered $ordered){
        return $this->createQueryBuilder('o')
            ->andWhere('o.product = :product')
            ->setParameter('product', $product)
            ->andWhere('o.ordered = :ordered')
            ->setParameter('ordered', $ordered)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return OrderedProducts[] Returns an array of OrderedProducts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderedProducts
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
