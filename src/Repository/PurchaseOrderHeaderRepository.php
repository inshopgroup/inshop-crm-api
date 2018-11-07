<?php

namespace App\Repository;

use App\Entity\PurchaseOrderHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PurchaseOrderHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseOrderHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseOrderHeader[]    findAll()
 * @method PurchaseOrderHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseOrderHeaderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PurchaseOrderHeader::class);
    }

//    /**
//     * @return PurchaseOrderHeader[] Returns an array of PurchaseOrderHeader objects
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
    public function findOneBySomeField($value): ?PurchaseOrderHeader
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
