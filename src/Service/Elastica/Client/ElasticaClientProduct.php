<?php

namespace App\Service\Elastica\Client;

use App\Entity\CategoryTranslation;
use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Service\ElasticaAdapter;
use App\Service\ElasticaPaginator;
use Elastica\Aggregation\Nested;
use Elastica\Aggregation\Range;
use Elastica\Aggregation\Terms;
use Elastica\Document;
use Elastica\Query;
use Elastica\Script\Script;
use Elastica\Type\Mapping;

/**
 * Class ElasticaClientProduct
 * @package App\Service\Elastica\Client
 */
class ElasticaClientProduct extends ElasticaClientBase
{
    /**
     * @return string
     */
    protected function getIndex()
    {
        return 'product';
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

        $mapping->setProperties(array(
            'id'      => array('type' => 'integer'),
            'search'     => array('type' => 'text', 'analyzer' => 'index_tokenizer_analyzer', 'search_analyzer' => 'search_analyzer'),
            'translations'    => array(
                'type' => 'object',
                'properties' => array(
                    'lang'      => array('type' => 'text', 'analyzer' => 'index_keyword_analyzer'),
                    'slug'      => array('type' => 'text', 'analyzer' => 'index_keyword_analyzer'),
                    'name'      => array('type' => 'text'),
                    'description'      => array('type' => 'text'),
                ),
            ),
            'categoryId'      => array('type' => 'integer'),
            'categoryTranslations'    => array(
                'type' => 'nested',
                'properties' => array(
                    'lang'      => array('type' => 'text', 'analyzer' => 'index_keyword_analyzer'),
                    'slug'      => array('type' => 'text', 'analyzer' => 'index_keyword_analyzer'),
                    'name'      => array('type' => 'text',  'fielddata' => true, 'analyzer' => 'index_keyword_analyzer'),
                ),
            ),
        ));

        // Send mapping to type
        $mapping->send();
    }

    /**
     * @param Product $entity
     * @return array
     */
    public function toArray(Product $entity): array
    {
        $categoryTranslations = [];

        /** @var CategoryTranslation $categoryTranslation */
        foreach ($entity->getCategory()->getTranslations() as $categoryTranslation) {
            $categoryTranslations[] = [
              'lang' => $categoryTranslation->getLanguage()->getCode(),
              'slug' => $categoryTranslation->getSlug(),
              'name' => $categoryTranslation->getName(),
            ];
        }

        $translations = [];

        /** @var ProductTranslation $translation */
        foreach ($entity->getTranslations() as $translation) {
            $translations[] = [
              'lang' => $translation->getLanguage()->getCode(),
              'slug' => $translation->getSlug(),
              'name' => $translation->getName(),
              'description' => $translation->getDescription(),
            ];
        }

        return [
            'id' => $entity->getId(),
            'translations' => $translations,
            'categoryId' => $entity->getCategory()->getId(),
            'categoryTranslations' => $categoryTranslations,
        ];
    }

    /**
     * @param Query $query
     * @return Query
     */
    protected function addAggregations(Query $query): Query
    {
//        $agg = new Nested('cities', 'cities');
//        $terms = new Terms('id');
//        $script = new Script("doc['cities.id'].value + '|' + doc['cities.name'].value");
//        $terms->setScript($script);
//        $terms->setSize(30);
//        $agg->addAggregation($terms);
//        $query->addAggregation($agg);
//
//        $agg = new Nested('skills', 'skills');
//        $terms = new Terms('id');
//        $script = new Script("doc['skills.id'].value + '|' + doc['skills.name'].value");
//        $terms->setScript($script);
//        $terms->setSize(30);
//        $agg->addAggregation($terms);
//        $query->addAggregation($agg);
//
//        $agg = new Nested('category', 'category');
//        $terms = new Terms('id');
//        $script = new Script("doc['category.id'].value + '|' + doc['category.name'].value");
//        $terms->setScript($script);
//        $terms->setSize(30);
//        $agg->addAggregation($terms);
//        $query->addAggregation($agg);
//
//        $agg = new Nested('englishLevel', 'englishLevel');
//        $terms = new Terms('id');
//        $script = new Script("doc['englishLevel.id'].value + '|' + doc['englishLevel.name'].value");
//        $terms->setScript($script);
//        $terms->setSize(30);
//        $agg->addAggregation($terms);
//        $query->addAggregation($agg);
//
//        $agg = new Nested('authorizationStatus', 'authorizationStatus');
//        $terms = new Terms('id');
//        $script = new Script("doc['authorizationStatus.id'].value + '|' + doc['authorizationStatus.name'].value");
//        $terms->setScript($script);
//        $terms->setSize(30);
//        $agg->addAggregation($terms);
//        $query->addAggregation($agg);
//
//        $agg = new Range('salary');
//        $agg->setField('salary');
//        $agg->addRange(0, 1000);
//        $agg->addRange(1000, 2000);
//        $agg->addRange(2000, 3000);
//        $agg->addRange(3000, 4000);
//        $agg->addRange(4000, 5000);
//        $agg->addRange(5000);
//        $query->addAggregation($agg);
//
//        $agg = new Range('yearsOfExperience');
//        $agg->setField('yearsOfExperience');
//        $agg->addRange(0, 1);
//        $agg->addRange(1);
//        $agg->addRange(2);
//        $agg->addRange(3);
//        $agg->addRange(5);
//        $query->addAggregation($agg);

        return $query;
    }

    /**
     * @param array $params
     * @return ElasticaPaginator
     */
    public function search(array $params): ElasticaPaginator
    {
        # Hits query
        $query = new Query();

        $boolQuery = new Query\BoolQuery();

        if (isset($params['q'])) {
            $term = new Query\Term();
            $term->setTerm('search', $params['q']);
            $boolQuery->addMust($term);
        }
//
//        if (isset($params['cities'])) {
//            $term = new Query\Terms();
//            $term->setTerms('cities.id', explode(',', $params['cities']));
//
//            $nested = new Query\Nested();
//            $nested->setPath('cities');
//            $nested->setQuery($term);
//
//            $boolQuery->addMust($nested);
//        }
//
//        if (isset($params['skills'])) {
//            $term = new Query\Terms();
//            $term->setTerms('skills.id', explode(',', $params['skills']));
//
//            $nested = new Query\Nested();
//            $nested->setPath('skills');
//            $nested->setQuery($term);
//
//            $boolQuery->addMust($nested);
//        }
//
//        if (isset($params['categories'])) {
//            $term = new Query\Terms();
//            $term->setTerms('category.id', explode(',', $params['categories']));
//
//            $nested = new Query\Nested();
//            $nested->setPath('category');
//            $nested->setQuery($term);
//
//            $boolQuery->addMust($nested);
//        }
//
//        if (isset($params['englishLevel'])) {
//            $term = new Query\Terms();
//            $term->setTerms('englishLevel.id', explode(',', $params['englishLevel']));
//
//            $nested = new Query\Nested();
//            $nested->setPath('englishLevel');
//            $nested->setQuery($term);
//
//            $boolQuery->addMust($nested);
//        }
//
//        if (isset($params['authorizationStatus'])) {
//            $term = new Query\Terms();
//            $term->setTerms('authorizationStatus.id', explode(',', $params['authorizationStatus']));
//
//            $nested = new Query\Nested();
//            $nested->setPath('authorizationStatus');
//            $nested->setQuery($term);
//
//            $boolQuery->addMust($nested);
//        }

        $query->setQuery($boolQuery);

        $page = isset($params['page']) ? $params['page'] : 1;
        $size = isset($params['perPage']) ? $params['perPage'] : 20;
        $from = $size * ($page - 1);

        $query
            ->setFrom($from)
            ->setSize($size)
//            ->setSort(['name' => 'asc'])
//            ->setSource(['obj1.*', 'obj2.'])
//            ->setFields(['name', 'created'])
//            ->setScriptFields($scriptFields) // $scriptFields instanceof Elastica\ScriptFields
//            ->setHighlight(['fields' => 'content'])
//            ->setRescore($rescoreQuery) // $rescoreQuery instanceof Elastica\Rescore\AbstractRescore
//            ->setExplain(false)
//            ->setVersion(false)
//            ->setPostFilter($filterTerm) // $$filterTerm instanceof Elastica\Filter\AbstractFilter
//            ->setMinScore(0.5)
        ;

        $search = $this->client->createSearch($this->getIndex());
        $search->setQuery($query);

        $results = $search->search();

        $data = array_map(function (Document $document) {
            return $document->toArray()['_source'];
        }, $results->getDocuments());

        $adapter = new ElasticaAdapter($data);
        $adapter->setNbResults($results->getTotalHits());

        // Facets
        $query = new Query();
        $query->setSize(0);
        $this->addAggregations($query);

        $search = $this->client->createSearch($this->getIndex());
        $search->setQuery($query);

        # Elastica paginator
        $elasticaPaginator = new ElasticaPaginator($adapter);
        $elasticaPaginator->setCurrentPage($page);
        $elasticaPaginator->setMaxPerPage($size);
        $elasticaPaginator->setAggregations($search->search()->getAggregations());

        return $elasticaPaginator;
    }
}
