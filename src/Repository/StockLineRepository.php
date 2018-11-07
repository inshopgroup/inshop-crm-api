<?php

namespace App\Repository;

use App\Entity\StockLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StockLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockLine[]    findAll()
 * @method StockLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockLineRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StockLine::class);
    }

//    /**
//     * @return StockLine[] Returns an array of StockLine objects
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
    public function findOneBySomeField($value): ?StockLine
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
