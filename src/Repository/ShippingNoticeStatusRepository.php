<?php

namespace App\Repository;

use App\Entity\ShippingNoticeStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ShippingNoticeStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShippingNoticeStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShippingNoticeStatus[]    findAll()
 * @method ShippingNoticeStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShippingNoticeStatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ShippingNoticeStatus::class);
    }

//    /**
//     * @return ShippingNoticeStatus[] Returns an array of ShippingNoticeStatus objects
//     */
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
    public function findOneBySomeField($value): ?ShippingNoticeStatus
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
