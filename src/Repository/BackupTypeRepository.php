<?php

namespace App\Repository;

use App\Entity\BackupType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BackupType|null find($id, $lockMode = null, $lockVersion = null)
 * @method BackupType|null findOneBy(array $criteria, array $orderBy = null)
 * @method BackupType[]    findAll()
 * @method BackupType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackupTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BackupType::class);
    }

//    /**
//     * @return BackupType[] Returns an array of BackupType objects
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
    public function findOneBySomeField($value): ?BackupType
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
