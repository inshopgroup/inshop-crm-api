<?php

namespace App\Repository;

use App\Entity\BackupStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BackupStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method BackupStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method BackupStatus[]    findAll()
 * @method BackupStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackupStatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BackupStatus::class);
    }

//    /**
//     * @return BackupStatus[] Returns an array of BackupStatus objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BackupStatus
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
