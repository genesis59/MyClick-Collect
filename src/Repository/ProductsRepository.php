<?php

namespace App\Repository;

use App\Entity\Products;
use App\Entity\Shops;
use App\Entity\ShopSubCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function getProductsByShopBySubCat(Shops $shop,ShopSubCategories $subCat = null){
        if(!$subCat){
            return $this->createQueryBuilder('p')
                        ->where('p.shop = :shop')
                        ->andwhere('p.subCategory IS NULL')
                        ->setParameter('shop', $shop)
                        ->getQuery()
                        ->getResult();
        }
        return $this->createQueryBuilder('p')
            ->where('p.shop = :shop')
            ->andWhere('p.subCategory = :subcat')
            ->setParameter('shop', $shop)
            ->setParameter('subcat', $subCat)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Products[] Returns an array of Products objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Products
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
