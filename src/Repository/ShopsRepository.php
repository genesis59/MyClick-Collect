<?php

namespace App\Repository;

use App\Entity\Shops;
use App\Entity\Towns;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Laminas\Code\Scanner\TokenArrayScanner;

/**
 * @method Shops|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shops|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shops[]    findAll()
 * @method Shops[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shops::class);
    }

    public function findShopByUser(User $user)
    {
        return $this->createQueryBuilder('s')
            ->where('s.trader=:user')
            ->setParameter('user', $user)
            ->getQuery()->execute();
    }

    /**
     * Undocumented function
     *
     * @param Towns $town
     * @return Shops[]
     */
    public function getShopByTown(Towns $town)
    {
        return $this->createQueryBuilder('s')
            ->where('s.town=:town')
            ->setParameter('town', $town)
            ->getQuery()->getResult();
    }

    // /**
    //  * @return Shops[] Returns an array of Shops objects
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
    public function findOneBySomeField($value): ?Shops
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
