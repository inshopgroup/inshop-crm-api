<?php

namespace App\Interfaces;

/**
 * Interface ElasticInterface
 * @package App\Interfaces
 */
interface ElasticInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return array
     */
    public function toArray(): array;
}
