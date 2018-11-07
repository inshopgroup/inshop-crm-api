<?php

namespace App\Repository;

use App\Entity\ShipmentMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ShipmentMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentMethod[]    findAll()
 * @method ShipmentMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentMethodRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ShipmentMethod::class);
    }

//    /**
//     * @return ShipmentMethod[] Returns an array of ShipmentMethod objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShipmentMethod
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
