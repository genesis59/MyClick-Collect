<?php

namespace App\Repository;

use App\Entity\Ordered;
use App\Entity\Shops;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ordered|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ordered|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ordered[]    findAll()
 * @method Ordered[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ordered::class);
    }

    public function getOrderedByShopAndUser(Shops $shop, User $user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->andWhere('o.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Ordered[] Returns an array of Ordered objects
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
    public function findOneBySomeField($value): ?Ordered
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
