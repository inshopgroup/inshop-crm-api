<?php

namespace App\Repository;

use App\Entity\PurchaseOrderLineStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PurchaseOrderLineStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseOrderLineStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseOrderLineStatus[]    findAll()
 * @method PurchaseOrderLineStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseOrderLineStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrderLineStatus::class);
    }

//    /**
//     * @return PurchaseOrderLineStatus[] Returns an array of PurchaseOrderLineStatus objects
//     */
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
    public function findOneBySomeField($value): ?PurchaseOrderLineStatus
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
