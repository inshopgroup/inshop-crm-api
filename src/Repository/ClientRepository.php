<?php

namespace App\Repository;

use App\Entity\Client;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getSummaryNew($days)
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
                ,
                $rsm
            );

        $query->setParameters(
            [
                'start' => (new DateTime())->modify(-$days . ' days'),
                'now' => new DateTime(),
            ]
        );

        return $query->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function findByToken(string $token): ?Client
    {
        return $this->createQueryBuilder('c')
            ->where('c.token = :token')
            ->andWhere('c.token is not null')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getClientByEmail(string $email): ?Client
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
