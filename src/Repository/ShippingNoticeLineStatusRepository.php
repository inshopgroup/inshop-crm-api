<?php

namespace App\Repository;

use App\Entity\ShippingNoticeLineStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ShippingNoticeLineStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShippingNoticeLineStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShippingNoticeLineStatus[]    findAll()
 * @method ShippingNoticeLineStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShippingNoticeLineStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShippingNoticeLineStatus::class);
    }

//    /**
//     * @return ShippingNoticeLineStatus[] Returns an array of ShippingNoticeLineStatus objects
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
    public function findOneBySomeField($value): ?ShippingNoticeLineStatus
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
