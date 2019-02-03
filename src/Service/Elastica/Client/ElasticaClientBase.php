<?php

namespace App\Service\Elastica\Client;

use ApiPlatform\Core\Api\IriConverterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Document;
use Elastica\Query;
use Elastica\QueryBuilder;
use Elastica\ResultSet;

/**
 * Class ElasticaClientBase
 * @package App\Service\Elastica\Client
 */
abstract class ElasticaClientBase
{
    /**
     * @return string
     */
    abstract protected function getIndex();

    /**
     * @var ElasticaClient
     */
    protected $client;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var IriConverterInterface
     */
    protected $iriConverter;

    /**
     * ElasticaClientBase constructor.
     * @param ElasticaClient $client
     * @param EntityManagerInterface $em
     * @param IriConverterInterface $iriConverter
     */
    public function __construct(ElasticaClient $client, EntityManagerInterface $em, IriConverterInterface $iriConverter)
    {
        $this->client = $client;
        $this->em = $em;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @return ElasticaClient
     */
    public function getElasticaClient(): ElasticaClient
    {
        return $this->client;
    }

    /**
     * @param array $object
     */
    public function addDocument(array $object): void
    {
        $this->client->addDocument($this->getIndex(), $object);
    }

    /**
     * @param array $objects
     */
    public function addDocuments(array $objects): void
    {
        $this->client->addDocuments($this->getIndex(), $objects);
    }

    /**
     * @param $id
     */
    public function deleteDocument($id): void
    {
        $this->client->getClient()->deleteIds([$id], $this->getIndex(), ElasticaClient::INDEX_TYPE);
    }

    /**
     * Recreate search index
     */
    public function createIndex(): void
    {
        $elasticaIndex = $this->client->getClient()->getIndex($this->getIndex());

        $elasticaIndex->create(
            [
                'number_of_shards' => 2,
                'number_of_replicas' => 1,
                'analysis' => [
                    'analyzer' => [
                        'index_tokenizer_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'tokenizer',
                            'filter' => array('standard', 'lowercase')
                        ],
                        'index_keyword_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'keyword',
                            'filter' => array()
                        ],
                        'search_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => array('standard', 'lowercase', 'mySnowball')
                        ]
                    ],
                    'tokenizer' => [
                        'tokenizer' => [
                            'type' => 'ngram',
                            'min_gram' => 3,
                            'max_gram' => 10,
                            'token_chars' => [
                                'letter',
                                'digit',
                            ],
                        ],
                    ],
                    'filter' => [
                        'mySnowball' => array(
                            'type' => 'snowball',
                            'language' => 'English'
                        )
                    ]
                ]
            ],
            true
        );
    }

    /**
     * @param string $slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?Document
    {
        $qb = new QueryBuilder();

        $query = new Query();
        $query->setQuery(
            $qb->query()->bool()
                ->addMust(
                    $qb->query()->term(['translations.slug' => $slug])
                )
//                ->addMustNot(
//                    $qb->query()->exists('field1')
//                )
        );

        $search = $this->client->createSearch($this->getIndex());
        $search->setQuery($query);
        $documents = $search->search()->getDocuments();

        return array_shift($documents);
    }
}
