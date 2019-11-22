<?php

namespace App\Repository;

use App\Entity\ShippingNoticeHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ShippingNoticeHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShippingNoticeHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShippingNoticeHeader[]    findAll()
 * @method ShippingNoticeHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShippingNoticeHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShippingNoticeHeader::class);
    }

//    /**
//     * @return ShippingNoticeHeader[] Returns an array of ShippingNoticeHeader objects
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
    public function findOneBySomeField($value): ?ShippingNoticeHeader
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
