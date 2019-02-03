<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use Pagerfanta\Adapter\AdapterInterface;

/**
 * Class ElasticaAdapter
 * @package App\Service
 */
class ElasticaAdapter implements AdapterInterface
{
    private $array;
    private $nbResults;

    /**
     * Constructor.
     *
     * @param array $array The array.
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * @param mixed $nbResults
     */
    public function setNbResults($nbResults): void
    {
        $this->nbResults = $nbResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->nbResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return $this->array;
    }
}
