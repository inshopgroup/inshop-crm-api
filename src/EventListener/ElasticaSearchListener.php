<?php

namespace App\EventListener;

use App\Interfaces\SearchInterface;
use App\Service\Elastica\Client\ElasticaClientSearch;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Class ElasticaSearchListener
 * @package App\EventListener
 */
class ElasticaSearchListener
{
    /**
     * @var ElasticaClientSearch
     */
    protected $search;

    /**
     * ElasticaListener constructor.
     * @param ElasticaClientSearch $search
     */
    public function __construct(ElasticaClientSearch $search)
    {
        $this->search = $search;
    }

    /**
     * Looks for new objects that should be indexed.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof SearchInterface) {
            $this->search->addDocument($this->search->createElasticSearchObject($entity));
        }
    }

    /**
     * Looks for objects being updated that should be indexed or removed from the index.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof SearchInterface) {
            $this->search->addDocument($this->search->createElasticSearchObject($entity));
        }
    }

    /**
     * Delete objects preRemove instead of postRemove so that we have access to the id.  Because this is called
     * preRemove, first check that the entity is managed by Doctrine.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof SearchInterface) {
            $object = $this->search->createElasticSearchObject($entity);
            $this->search->deleteDocument($object->getId());
        }
    }
}
