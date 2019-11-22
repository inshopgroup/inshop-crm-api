<?php

namespace App\Repository;

use App\Entity\ShippingNoticeLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ShippingNoticeLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShippingNoticeLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShippingNoticeLine[]    findAll()
 * @method ShippingNoticeLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShippingNoticeLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShippingNoticeLine::class);
    }

//    /**
//     * @return ShippingNoticeLine[] Returns an array of ShippingNoticeLine objects
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
    public function findOneBySomeField($value): ?ShippingNoticeLine
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
