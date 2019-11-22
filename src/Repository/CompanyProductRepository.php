<?php

namespace App\Repository;

use App\Entity\CompanyProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CompanyProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyProduct[]    findAll()
 * @method CompanyProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyProduct::class);
    }

//    /**
//     * @return CompanyProduct[] Returns an array of CompanyProduct objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompanyProduct
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
