<?php

namespace App\Service;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use Pagerfanta\Pagerfanta;

/**
 * Class ElasticaPaginator
 * @package App\Service
 */
class ElasticaPaginator extends Pagerfanta implements PaginatorInterface
{
    // https://github.com/api-platform/core/issues/1879

    /**
     * @var array
     */
    protected $aggregations = [];

    /**
     * @return float
     */
    public function getCurrentPage(): float
    {
        return parent::getCurrentPage();
    }

    /**
     * @return float
     */
    public function getLastPage(): float
    {
        return parent::getNbPages();
    }

    /**
     * @return float
     */
    public function getTotalItems(): float
    {
        return parent::getNbResults();
    }

    /**
     * @return float
     */
    public function getItemsPerPage(): float
    {
        return parent::getMaxPerPage();
    }

    /**
     * @return array
     */
    public function getAggregations(): array
    {
        return $this->aggregations;
    }

    /**
     * @param array $aggregations
     */
    public function setAggregations(array $aggregations): void
    {
        $this->aggregations = $aggregations;
    }
}
