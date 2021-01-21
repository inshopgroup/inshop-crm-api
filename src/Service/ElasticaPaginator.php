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
    protected array $aggregations = [];

    protected array $results = [];

    protected int $totalItems = 0;

    protected int $currentPage = 1;

    protected int $itemsPerPage = 30;

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->getTotalItems();
    }

    /**
     * @return ArrayIterator|mixed|Traversable
     * @throws Exception
     */
    public function getIterator(): Iterator
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
    public function jsonSerialize(): array
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
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param array $results
     */
    public function setResults(array $results): void
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
     * @param int $itemsPerPage
     */
    public function setItemsPerPage(int $itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
    }
}
