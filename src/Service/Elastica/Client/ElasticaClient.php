<?php

namespace App\Service\Elastica\Client;

use Elastica\Client;
use Elastica\Document;
use Elastica\Index;
use Elastica\Search;

/**
 * Class ElasticaClient
 * @package App\Service
 */
class ElasticaClient
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * ElasticaClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client(
            array(
                'host' => 'elasticsearch',
                'port' => 9200,
            )
        );
    }

    /**
     * @param string $index
     * @return Search
     */
    public function createSearch(string $index): Search
    {
        $search = new Search($this->client);
        $search->addIndex($index);

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
        $document = new Document($data['id'], $data, $index);
        $this->getIndex($index)->addDocument($document);
        $this->getIndex($index)->refresh();
    }

    /**
     * @param string $index
     * @param array $data
     */
    public function addDocuments(string $index, array $data): void
    {
        $documents = [];

        foreach ($data as $datum) {
            $documents[] = new Document($datum['id'], $datum, $index);
        }

        $this->getIndex($index)->addDocuments($documents);
        $this->getIndex($index)->refresh();
    }
}
