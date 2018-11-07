<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\TaskStatus;
use Doctrine\ORM\AbstractQuery;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return mixed
     */
    public function getSummary($days)
    {
        return $this
            ->createQueryBuilder('u')
            ->select('u.id, u.name, count(t.id) cnt')
            ->innerJoin('u.tasks', 't')
            ->where('t.deadline BETWEEN :start AND :now')
            ->andWhere('t.status = :status')
            ->setParameter('start', (new \DateTime())->modify(-$days . ' days'))
            ->setParameter('now', new \DateTime())
            ->setParameter('status', TaskStatus::STATUS_DONE)
            ->setMaxResults(10)
            ->groupBy('u.id')
            ->orderBy('cnt', 'desc')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
        ;
    }

    /**
     * @return mixed
     */
    public function getTimeSummary($days)
    {
        return $this
            ->createQueryBuilder('u')
            ->select('u.id, u.name, sum(t.timeSpent) / 60 cnt')
            ->innerJoin('u.tasks', 't')
            ->where('t.deadline BETWEEN :start AND :now')
            ->andWhere('t.status = :status')
            ->setParameter('start', (new \DateTime())->modify(-$days . ' days'))
            ->setParameter('now', new \DateTime())
            ->setParameter('status', TaskStatus::STATUS_DONE)
            ->setMaxResults(20)
            ->groupBy('u.id')
            ->orderBy('cnt', 'desc')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
            ;
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
