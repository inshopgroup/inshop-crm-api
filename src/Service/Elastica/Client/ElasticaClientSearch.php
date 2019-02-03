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
class ElasticaClientSearch
{
    public const INDEX = 'search';

    /**
     * @var ElasticaClient
     */
    protected $client;

    /**
     * @var IriConverterInterface
     */
    protected $iriConverter;

    /**
     * ElasticaClientSearch constructor.
     * @param ElasticaClient $client
     * @param IriConverterInterface $iriConverter
     */
    public function __construct(ElasticaClient $client, IriConverterInterface $iriConverter)
    {
        $this->client = $client;
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
     * @param ElasticInterface $object
     */
    public function addDocument(ElasticInterface $object): void
    {
        $this->client->addDocument(self::INDEX, $object);
    }

    /**
     * @param array $objects
     */
    public function addDocuments(array $objects): void
    {
        $this->client->addDocuments(self::INDEX, $objects);
    }

    /**
     * @param $id
     */
    public function deleteDocument($id): void
    {
        $this->client->getClient()->deleteIds([$id], self::INDEX, ElasticaClient::INDEX_TYPE);
    }

    /**
     * @param string $q
     * @return ResultSet
     */
    public function search(string $q): ResultSet
    {
        $query = new Query([
            'size' => 30,
            'query' => [
                'match' => [
                    'text' => [
                        'query' => $q,
                        'analyzer' => 'search_analyser'
                    ]
                ],
            ]
        ]);

        $search = $this->client->createSearch(self::INDEX);
        $search->setQuery($query);

        return $search->search();
    }

    /**
     * Recreate search index
     */
    public function createIndex(): void
    {
        $elasticaIndex = $this->client->getClient()->getIndex(self::INDEX);

        $elasticaIndex->create(
            [
                'number_of_shards' => 4,
                'number_of_replicas' => 1,
                'analysis' => [
                    'analyzer' => [
                        'default' => [
                            'type' => 'custom',
                            'tokenizer' => 'my_tokenizer',
                            'filter' => array('standard', 'lowercase')
                        ],
                        'search_analyser' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => array('standard', 'lowercase', 'mySnowball')
                        ]
                    ],
                    'tokenizer' => [
                        'my_tokenizer' => [
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
     * Create type & mapping
     */
    public function createMapping(): void
    {
        // Create a type
        $elasticaType = $this->client->getClient()->getIndex(self::INDEX)->getType('_doc');

        // Define mapping
        $mapping = new Mapping();
        $mapping->setType($elasticaType);

        // Set mapping
        $mapping->setProperties(array(
            'id'       => array('type' => 'text'),
            'iri'      => array('type' => 'text'),
            'entityId' => array('type' => 'integer'),
            'type'     => array('type' => 'text'),
            'text'     => array('type' => 'text'),
        ));

        // Send mapping to type
        $mapping->send();
    }

    /**
     * @param SearchInterface $entity
     * @return ElasticSearch
     */
    public function createElasticSearchObject(SearchInterface $entity): ElasticSearch
    {
        $class = self::getEntityClass($entity);

        $search = new ElasticSearch();
        $search->setId(strtolower($class) . '_' . $entity->getId());
        $search->setIri($this->iriConverter->getIriFromItem($entity));
        $search->setEntityId($entity->getId());
        $search->setType($class);
        $search->setText($entity->getSearchText());

        return $search;
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
