<?php

namespace App\Service\Elastica\Client;

use ApiPlatform\Core\Api\IriConverterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Document;
use Elastica\Query;
use Elastica\QueryBuilder;

/**
 * Class ElasticaClientBase
 * @package App\Service\Elastica\Client
 */
abstract class ElasticaClientBase
{
    /**
     * @return string
     */
    abstract protected function getIndex(): string;

    /**
     * @var ElasticaClient
     */
    protected ElasticaClient $client;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var IriConverterInterface
     */
    protected IriConverterInterface $iriConverter;

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
        $this->client->getClient()->deleteIds([$id], $this->getIndex());
    }

    /**
     * Recreate search index
     */
    public function createIndex(): void
    {
        $elasticaIndex = $this->client->getIndex($this->getIndex());
        $elasticaIndex->create(
            [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 1,
                    'max_ngram_diff' => 17,
                    'analysis' => [
                        'analyzer' => [
                            'index_keyword_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'keyword',
                                'filter' => array(),
                            ],
                            'analyzer_ngram' => [
                                'type' => 'custom',
                                'tokenizer' => 'my_tokenizer',
                                'filter' => array('lowercase'),
                            ],
                            'analyzer_whitespace' => [
                                'type' => 'custom',
                                'tokenizer' => 'whitespace',
                                'filter' => array('lowercase'),
                            ],
                            'search_analyser' => [
                                'type' => 'custom',
                                'tokenizer' => 'whitespace',
                                'filter' => array('lowercase'),
                            ],
                        ],
                        'tokenizer' => [
                            'my_tokenizer' => [
                                'type' => 'ngram',
                                'min_gram' => 3,
                                'max_gram' => 20,
                                'token_chars' => [
                                    'letter',
                                    'digit',
                                    'symbol',
                                ],
                            ],
                        ],
                    ],
                ],
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
                    $qb->query()->term(['slug' => $slug])
                )
        );

        $search = $this->client->createSearch($this->getIndex());
        $search->setQuery($query);

        $documents = $search->search()->getDocuments();

        return array_shift($documents);
    }

    /**
     * @param string $q
     * @return Query\BoolQuery
     */
    protected function getKeywordQuery(string $q): Query\BoolQuery
    {
        $q = mb_strtolower(trim(rawurldecode($q)));
        $words = explode(' ', $q);

        $bqq = new Query\BoolQuery();
        $bqw = new Query\BoolQuery();

        foreach ($words as $word) {
            $word = trim($word);

            if (!empty($word)) {
                $bq = new Query\BoolQuery();

                $fuzzy = new Query\Fuzzy();
                $fuzzy->setField('search_whitespace', $word);

                $match = new Query\Match();
                $match->setFieldQuery('search_ngram', $word);
                $match->setFieldAnalyzer('search_ngram', 'search_analyser');

                $bq->addShould($fuzzy);
                $bq->addShould($match);

                $bqw->addMust($bq);
            }
        }

        $wildcard = new Query\Wildcard('search', '*' . $q . '*');
        $bqq->addShould($wildcard);
        $bqq->addShould($bqw);

        return $bqq;
    }
}
