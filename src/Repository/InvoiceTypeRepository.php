<?php

namespace App\Repository;

use App\Entity\InvoiceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method InvoiceType|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceType|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceType[]    findAll()
 * @method InvoiceType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, InvoiceType::class);
    }

//    /**
//     * @return InvoiceType[] Returns an array of InvoiceType objects
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
    public function findOneBySomeField($value): ?InvoiceType
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
