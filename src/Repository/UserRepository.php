<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $days
     * @return mixed
     * @throws \Exception
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
     * @param $days
     * @return mixed
     * @throws \Exception
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
}
