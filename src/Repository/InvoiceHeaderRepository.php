<?php

namespace App\Repository;

use App\Entity\InvoiceHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InvoiceHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceHeader[]    findAll()
 * @method InvoiceHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceHeader::class);
    }

//    /**
//     * @return InvoiceHeader[] Returns an array of InvoiceHeader objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InvoiceHeader
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
