<?php

namespace App\Repository;

use App\Entity\Text;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Text|null find($id, $lockMode = null, $lockVersion = null)
 * @method Text|null findOneBy(array $criteria, array $orderBy = null)
 * @method Text[]    findAll()
 * @method Text[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Text::class);
    }

    /**
     * @param string $value
     * @return Text|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySlug(string $value): ?Text
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.translations', 'tt')
            ->andWhere('tt.slug = :slug')
            ->setParameter('slug', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Text
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
