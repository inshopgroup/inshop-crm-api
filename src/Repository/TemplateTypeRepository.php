<?php

namespace App\Repository;

use App\Entity\TemplateType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TemplateType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateType[]    findAll()
 * @method TemplateType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateType::class);
    }

    // /**
    //  * @return TemplateType[] Returns an array of TemplateType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TemplateType
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
