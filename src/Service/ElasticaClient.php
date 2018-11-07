<?php

namespace App\Service;

use App\Interfaces\ElasticInterface;
use Elastica\Client;
use Elastica\Document;
use Elastica\Index;
use Elastica\Search;
use Elastica\Type;

/**
 * Class ElasticaClient
 * @package App\Service
 */
class ElasticaClient
{
    public const INDEX_TYPE = '_doc';

    /**
     * @var Client
     */
    protected $client;

    /**
     * ElasticaClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client(array(
            'host' => 'elasticsearch',
            'port' => 9200
        ));
    }

    /**
     * @param string $index
     * @return Search
     */
    public function createSearch(string $index): Search
    {
        $search = new Search($this->client);
        $search->addIndex($index);
        $search->addType('_doc');

        return $search;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param string $index
     * @return Type
     */
    public function getType(string $index): Type
    {
        return $this->client->getIndex($index)->getType(self::INDEX_TYPE);
    }

    /**
     * @param string $index
     * @return Index
     */
    public function getIndex(string $index): Index
    {
        return $this->client->getIndex($index);
    }

    /**
     * @param string $index
     * @param ElasticInterface $obejct
     */
    public function addDocument(string $index, ElasticInterface $obejct): void
    {
        $document = new Document($obejct->getId(), $obejct->toArray());
        $this->getType($index)->addDocument($document);
        $this->getType($index)->getIndex()->refresh();
    }

    /**
     * @param string $index
     * @param array $objects
     */
    public function addDocuments(string $index, array $objects): void
    {
        $documents = [];

        foreach ($objects as $object) {
            if ($object instanceof ElasticInterface) {
                $documents[] = new Document($object->getId(), $object->toArray());
            }
        }

        $this->getType($index)->addDocuments($documents);
        $this->getType($index)->getIndex()->refresh();
    }
}
