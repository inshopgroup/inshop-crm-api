<?php

namespace App\Service\Elastica\Client;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\ElasticSearch;
use App\Interfaces\ElasticInterface;
use App\Interfaces\SearchInterface;
use Elastica\Query;
use Elastica\ResultSet;
use Elastica\Type\Mapping;

/**
 * Class ElasticaClientSearch
 * @package App\Service
 */
class ElasticaClientSearch extends ElasticaClientBase
{
    /**
     * @return string
     */
    protected function getIndex()
    {
        return 'search';
    }

    /**
     * Create type & mapping
     */
    public function createMapping(): void
    {
        // Create a type
        $elasticaType = $this->client->getClient()->getIndex($this->getIndex())->getType('_doc');

        // Define mapping
        $mapping = new Mapping();
        $mapping->setType($elasticaType);

        // Set mapping
        $mapping->setProperties([
            'id'       => array('type' => 'text'),
            'iri'      => array('type' => 'text'),
            'entityId' => array('type' => 'integer'),
            'type'     => array('type' => 'text'),
            'text'     => array('type' => 'text'),
        ]);

        // Send mapping to type
        $mapping->send();
    }

    /**
     * @param SearchInterface $entity
     * @return array
     */
    public function toArray(SearchInterface $entity): array
    {
        $class = self::getEntityClass($entity);

        return [
            'id'       => strtolower($class) . '_' . $entity->getId(),
            'iri'      => $this->iriConverter->getIriFromItem($entity),
            'entityId' => $entity->getId(),
            'type'     => $class,
            'text'     => $entity->getSearchText(),
        ];
    }

    /**
     * @param SearchInterface $entity
     * @return mixed
     */
    protected static function getEntityClass(SearchInterface $entity)
    {
        $path = explode('\\', \get_class($entity));

        return array_pop($path);
    }
}
