<?php

namespace App\Service\Elastica\Client;

use App\Entity\City;
use App\Entity\Product;
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
            'slug'     => array('type' => 'text', 'analyzer' => 'index_keyword_analyzer'),
//            'name'     => array('type' => 'text', 'copy_to' => 'search', 'boost' => 2),
            'title'     => array('type' => 'text', 'copy_to' => 'search', 'boost' => 2),
            'status'    => array(
                'type' => 'object',
                'properties' => array(
                    'id'      => array('type' => 'integer'),
                    'name'      => array('type' => 'text'),
                ),
            ),
            'salary'     => array('type' => 'integer'),
            'cities'    => array(
                'type' => 'nested',
                'properties' => array(
                    'id'      => array('type' => 'integer'),
                    'name'      => array('type' => 'text',  'fielddata' => true, 'analyzer' => 'index_keyword_analyzer'),
                ),
            ),
            'skills'    => array(
                'type' => 'nested',
                'properties' => array(
                    'id'      => array('type' => 'integer'),
                    'name'      => array('type' => 'text',  'fielddata' => true, 'analyzer' => 'index_keyword_analyzer'),
                    'level'      => array('type' => 'integer'),
                ),
            ),
            'description'     => array('type' => 'text', 'copy_to' => 'search'),
            'yearsOfExperience '     => array('type' => 'integer'),
            'category'    => array(
                'type' => 'nested',
                'properties' => array(
                    'id'      => array('type' => 'integer'),
                    'name'      => array('type' => 'text',  'fielddata' => true, 'analyzer' => 'index_keyword_analyzer'),
                ),
            ),
            'additionalCategory'    => array(
                'type' => 'object',
                'properties' => array(
                    'id'      => array('type' => 'integer'),
                    'name'      => array('type' => 'text'),
                ),
            ),
            'englishLevel'    => array(
                'type' => 'nested',
                'properties' => array(
                    'id'      => array('type' => 'integer'),
                    'name'      => array('type' => 'text',  'fielddata' => true, 'analyzer' => 'index_keyword_analyzer'),
                ),
            ),
            'authorizationStatus'    => array(
                'type' => 'nested',
                'properties' => array(
                    'id'      => array('type' => 'integer'),
                    'name'      => array('type' => 'text',  'fielddata' => true, 'analyzer' => 'index_keyword_analyzer'),
                ),
            ),
            'readyFullTimeOffice'  => array('type' => 'boolean'),
            'readyFreelance'  => array('type' => 'boolean'),
            'readyRemote'  => array('type' => 'boolean'),
            'readyRelocateUsa'  => array('type' => 'boolean'),
            'readyRelocateState'  => array('type' => 'boolean'),
            'readyRelocateOtherCountry'  => array('type' => 'boolean'),
            'lookingFor'  => array('type' => 'text'),
            'highlights'  => array('type' => 'text'),
//            'skype'     => array('type' => 'text'),
//            'phone'     => array('type' => 'text'),
//            'telegram'     => array('type' => 'text'),
//            'linkedin'     => array('type' => 'text'),
//            'github'     => array('type' => 'text'),
            'createdAt'  => array('type' => 'text'),
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
        $_cities = [];
        $_skills = [];

        /** @var City $city */
        foreach ($entity->getCities() as $city) {
            $_cities[] = [
                'id' => $city->getId(),
                'name' => $city->getName(),
            ];
        }

        /** @var Skill $skill */
        foreach ($entity->getCandidateSkills() as $candidateSkill) {
            $_skills[] = [
                'id' => $candidateSkill->getSkill()->getId(),
                'name' => $candidateSkill->getSkill()->getName(),
                'level' => $candidateSkill->getLevel(),
            ];
        }

        $additionalCategory = null;
        if ($entity->getCategory()) {
            $additionalCategory = [
                'id' => $entity->getCategory()->getId(),
                'name' => $entity->getCategory()->getName(),
            ];
        }

        return [
            'id' => $entity->getId(),
            'slug' => $entity->getSlug(),
            'title' => $entity->getTitle(),
            'status' => [
                'id' => $entity->getStatus()->getId(),
                'name' => $entity->getStatus()->getName(),
            ],
            'salary' => $entity->getSalary(),
            'cities' => $_cities,
            'skills' => $_skills,
            'description' => $entity->getDescription(),
            'yearsOfExperience' => $entity->getYearsOfExperience(),
            'category' => [
                'id' => $entity->getCategory()->getId(),
                'name' => $entity->getCategory()->getName(),
            ],
            'additionalCategory' => $additionalCategory,
            'englishLevel' => [
                'id' => $entity->getEnglishLevel()->getId(),
                'name' => $entity->getEnglishLevel()->getName(),
            ],
            'authorizationStatus' => [
                'id' => $entity->getAuthorizationStatus()->getId(),
                'name' => $entity->getAuthorizationStatus()->getName(),
            ],
            'readyFullTimeOffice' => $entity->getReadyFullTimeOffice(),
            'readyFreelance' => $entity->getReadyFreelance(),
            'readyRemote' => $entity->getReadyRemote(),
            'readyRelocateUsa' => $entity->getReadyRelocateUsa(),
            'readyRelocateState' => $entity->getReadyRelocateState(),
            'readyRelocateOtherCountry' => $entity->getReadyRelocateOtherCountry(),
            'lookingFor' => $entity->getLookingFor(),
            'highlights' => $entity->getHighlights(),
//            'skype' => $entity->getSkype(),
//            'phone' => $entity->getPhone(),
//            'telegram' => $entity->getLinkedin(),
//            'linkedin' => $entity->getLinkedin(),
//            'github' => $entity->getLinkedin(),
            'createdAt' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updateAt' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Query $query
     * @return Query
     */
    protected function addAggregations(Query $query): Query
    {
        $agg = new Nested('cities', 'cities');
        $terms = new Terms('id');
        $script = new Script("doc['cities.id'].value + '|' + doc['cities.name'].value");
        $terms->setScript($script);
        $terms->setSize(30);
        $agg->addAggregation($terms);
        $query->addAggregation($agg);

        $agg = new Nested('skills', 'skills');
        $terms = new Terms('id');
        $script = new Script("doc['skills.id'].value + '|' + doc['skills.name'].value");
        $terms->setScript($script);
        $terms->setSize(30);
        $agg->addAggregation($terms);
        $query->addAggregation($agg);

        $agg = new Nested('category', 'category');
        $terms = new Terms('id');
        $script = new Script("doc['category.id'].value + '|' + doc['category.name'].value");
        $terms->setScript($script);
        $terms->setSize(30);
        $agg->addAggregation($terms);
        $query->addAggregation($agg);

        $agg = new Nested('englishLevel', 'englishLevel');
        $terms = new Terms('id');
        $script = new Script("doc['englishLevel.id'].value + '|' + doc['englishLevel.name'].value");
        $terms->setScript($script);
        $terms->setSize(30);
        $agg->addAggregation($terms);
        $query->addAggregation($agg);

        $agg = new Nested('authorizationStatus', 'authorizationStatus');
        $terms = new Terms('id');
        $script = new Script("doc['authorizationStatus.id'].value + '|' + doc['authorizationStatus.name'].value");
        $terms->setScript($script);
        $terms->setSize(30);
        $agg->addAggregation($terms);
        $query->addAggregation($agg);

        $agg = new Range('salary');
        $agg->setField('salary');
        $agg->addRange(0, 1000);
        $agg->addRange(1000, 2000);
        $agg->addRange(2000, 3000);
        $agg->addRange(3000, 4000);
        $agg->addRange(4000, 5000);
        $agg->addRange(5000);
        $query->addAggregation($agg);

        $agg = new Range('yearsOfExperience');
        $agg->setField('yearsOfExperience');
        $agg->addRange(0, 1);
        $agg->addRange(1);
        $agg->addRange(2);
        $agg->addRange(3);
        $agg->addRange(5);
        $query->addAggregation($agg);

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

        if (isset($params['cities'])) {
            $term = new Query\Terms();
            $term->setTerms('cities.id', explode(',', $params['cities']));

            $nested = new Query\Nested();
            $nested->setPath('cities');
            $nested->setQuery($term);

            $boolQuery->addMust($nested);
        }

        if (isset($params['skills'])) {
            $term = new Query\Terms();
            $term->setTerms('skills.id', explode(',', $params['skills']));

            $nested = new Query\Nested();
            $nested->setPath('skills');
            $nested->setQuery($term);

            $boolQuery->addMust($nested);
        }

        if (isset($params['categories'])) {
            $term = new Query\Terms();
            $term->setTerms('category.id', explode(',', $params['categories']));

            $nested = new Query\Nested();
            $nested->setPath('category');
            $nested->setQuery($term);

            $boolQuery->addMust($nested);
        }

        if (isset($params['englishLevel'])) {
            $term = new Query\Terms();
            $term->setTerms('englishLevel.id', explode(',', $params['englishLevel']));

            $nested = new Query\Nested();
            $nested->setPath('englishLevel');
            $nested->setQuery($term);

            $boolQuery->addMust($nested);
        }

        if (isset($params['authorizationStatus'])) {
            $term = new Query\Terms();
            $term->setTerms('authorizationStatus.id', explode(',', $params['authorizationStatus']));

            $nested = new Query\Nested();
            $nested->setPath('authorizationStatus');
            $nested->setQuery($term);

            $boolQuery->addMust($nested);
        }

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
