<?php

namespace App\Interfaces;

/**
 * Interface SearchInterface
 * @package App\Interfaces
 */
interface SearchInterface
{
    /**
     * Entity ID
     *
     * @return int
     */
    public function getId(): ?int;

    /**
     * Search text
     *
     * @return string
     */
    public function getSearchText(): string;
}
