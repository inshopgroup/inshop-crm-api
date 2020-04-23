<?php

namespace App\Service;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use ArrayIterator;
use Countable;
use Exception;
use Iterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Class ElasticaPaginator
 * @package App\Service
 */
class ElasticaPaginator implements Countable, IteratorAggregate, JsonSerializable, PaginatorInterface
{
    /**
     * @var array
     */
    protected $aggregations = [];

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var int
     */
    protected $totalItems = 0;

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var
     */
    protected $itemsPerPage = 30;

    /**
     * @return float|int
     */
    public function count()
    {
        return $this->getTotalItems();
    }

    /**
     * @return ArrayIterator|mixed|Traversable
     * @throws Exception
     */
    public function getIterator()
    {
        $results = $this->getResults();

        if ($results instanceof Iterator) {
            return $results;
        }

        if ($results instanceof IteratorAggregate) {
            return $results->getIterator();
        }

        return new ArrayIterator($results);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $results = $this->getResults();
        if ($results instanceof Traversable) {
            return iterator_to_array($results);
        }

        return $results;
    }


    /**
     * @return float
     */
    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    /**
     * @return float
     */
    public function getLastPage(): float
    {
        if ($this->getItemsPerPage() === 0) {
            return 0;
        }

        return ceil($this->getTotalItems() / $this->getItemsPerPage());
    }

    /**
     * @return float
     */
    public function getTotalItems(): float
    {
        return $this->totalItems;
    }

    /**
     * @return float
     */
    public function getItemsPerPage(): float
    {
        return $this->itemsPerPage;
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

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param mixed $results
     */
    public function setResults($results): void
    {
        $this->results = $results;
    }

    /**
     * @param int $totalItems
     */
    public function setTotalItems(int $totalItems): void
    {
        $this->totalItems = $totalItems;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @param mixed $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
    }
}
