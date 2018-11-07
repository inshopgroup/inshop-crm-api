<?php

namespace App\Repository;

use App\Entity\StockLineStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StockLineStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLineStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLineStatus[]    findAll()
 * @method StockLineStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLineStatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StockLineStatus::class);
    }

//    /**
//     * @return StockLineStatus[] Returns an array of StockLineStatus objects
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
    public function findOneBySomeField($value): ?StockLineStatus
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
