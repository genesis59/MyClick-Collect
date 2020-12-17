<?php

namespace App\Repository;

use App\Entity\Shops;
use App\Entity\ShopSubCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShopSubCategories|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopSubCategories|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopSubCategories[]    findAll()
 * @method ShopSubCategories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopSubCategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopSubCategories::class);
    }

    /**
     * Request for EntityType
     *
     * @param Shops $shop
     * @return void
     */
    public function findSubCategoriesByShopForBuilder(Shops $shop)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.shop = :val')
            ->setParameter('val', $shop)
        ;
    }

    // /**
    //  * @return ShopSubCategories[] Returns an array of ShopSubCategories objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShopSubCategories
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
