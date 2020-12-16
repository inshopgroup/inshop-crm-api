<?php

namespace App\Repository;

use App\Entity\OrderLineStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderLineStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderLineStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderLineStatus[]    findAll()
 * @method OrderLineStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderLineStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderLineStatus::class);
    }

//    /**
//     * @return OrderLineStatus[] Returns an array of OrderLineStatus objects
//     */
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
    public function findOneBySomeField($value): ?OrderLineStatus
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
