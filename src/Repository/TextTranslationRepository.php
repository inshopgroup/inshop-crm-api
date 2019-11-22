<?php

namespace App\Repository;

use App\Entity\TextTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TextTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextTranslation[]    findAll()
 * @method TextTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextTranslation::class);
    }

    // /**
    //  * @return TextTranslation[] Returns an array of TextTranslation objects
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
    public function findOneBySomeField($value): ?TextTranslation
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
