<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\TaskStatus;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return mixed
     */
    public function getDeadlines()
    {
        return $this
            ->createQueryBuilder('t')
            ->where('t.deadline <= :now')
            ->andWhere('t.status != :status')
            ->setParameter('now', new DateTime())
            ->setParameter('status', TaskStatus::STATUS_DONE)
            ->orderBy('t.deadline', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $days
     * @return mixed
     */
    public function getSummary($days)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('cnt', 'cnt');

        $query = $this
            ->getEntityManager()
            ->createNativeQuery(
                <<<SQL
                SELECT
                  days.name,
                  tmp.cnt
                FROM (
                  SELECT
                    to_char(date_trunc('day', (current_date - offs)), 'YYYY-MM-DD') AS name
                  FROM
                    generate_series(0, 29, 1) AS offs
                ) days
                  LEFT JOIN (
                    SELECT
                      to_char(date_trunc('day', t.deadline), 'YYYY-MM-DD') as name,
                      count(t.id) cnt
                    FROM
                      task t
                    WHERE
                      t.deadline BETWEEN :start AND :now AND 
                      t.status_id = :status
                    GROUP BY
                      to_char(date_trunc('day', t.deadline), 'YYYY-MM-DD')
                ) tmp ON days.name = tmp.name
                ORDER BY days.name ASC
SQL
                ,
                $rsm
            );

        $query->setParameters(
            [
                'start' => (new DateTime())->modify(-$days . ' days'),
                'now' => new DateTime(),
                'status' => TaskStatus::STATUS_DONE,
            ]
        );

        return $query->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
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
    public function findOneBySomeField($value): ?Task
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
