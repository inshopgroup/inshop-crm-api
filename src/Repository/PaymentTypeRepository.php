<?php

namespace App\Repository;

use App\Entity\PaymentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentType[]    findAll()
 * @method PaymentType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentType::class);
    }

//    /**
//     * @return PaymentType[] Returns an array of PaymentType objects
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
    public function findOneBySomeField($value): ?PaymentType
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
