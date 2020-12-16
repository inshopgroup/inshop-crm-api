<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Gedmo\Tool\Wrapper\EntityWrapper;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    /**
     * Loads all log entries for the given entity
     *
     * @param object $entity
     *
     * @return History[]
     */
    public function getLogEntries(object $entity): array
    {
        $q = $this->getLogEntriesQuery($entity);

        return $q->getResult();
    }

    /**
     * Get the query for loading of log entries
     *
     * @param object $entity
     *
     * @return Query
     */
    public function getLogEntriesQuery(object $entity): Query
    {
        $wrapped = new EntityWrapper($entity, $this->_em);
        $objectClass = $wrapped->getMetadata()->name;
        $meta = $this->getClassMetadata();
        $dql = "SELECT log FROM {$meta->name} log";
        $dql .= ' WHERE log.objectId = :objectId';
        $dql .= ' AND log.objectClass = :objectClass';
        $dql .= ' ORDER BY log.version DESC';

        $objectId = (string) $wrapped->getIdentifier();
        $q = $this->_em->createQuery($dql);
        $q->setParameters(compact('objectId', 'objectClass'));

        return $q;
    }
}
