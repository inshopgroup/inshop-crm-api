<?php

namespace App\Service\Elastica\Client;

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
     * @param array $data
     */
    public function addDocument(string $index, array $data): void
    {
        $document = new Document($data['id'], $data);
        $this->getType($index)->addDocument($document);
        $this->getType($index)->getIndex()->refresh();
    }

    /**
     * @param string $index
     * @param array $datas
     */
    public function addDocuments(string $index, array $datas): void
    {
        $documents = [];

        foreach ($datas as $data) {
            $documents[] = new Document($data['id'], $data);
        }

        $this->getType($index)->addDocuments($documents);
        $this->getType($index)->getIndex()->refresh();
    }
}
