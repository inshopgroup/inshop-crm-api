<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @return mixed
     */
    public function getSummaryNew($days)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('cnt', 'cnt');

        $query = $this
            ->getEntityManager()
            ->createNativeQuery(<<<SQL
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
                      to_char(date_trunc('day', c.created_at), 'YYYY-MM-DD') as name,
                      count(c.id) cnt
                    FROM
                      client c
                    WHERE
                      c.created_at BETWEEN :start AND :now 
                    GROUP BY
                      to_char(date_trunc('day', c.created_at), 'YYYY-MM-DD')
                ) tmp ON days.name = tmp.name
                ORDER BY days.name ASC
SQL
                , $rsm);

        $query->setParameters([
            'start' => (new \DateTime())->modify(-$days . ' days'),
            'now'   => new \DateTime(),
        ]);

        return $query->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

//    /**
//     * @return Client[] Returns an array of Client objects
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
    public function findOneBySomeField($value): ?Client
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
