<?php

namespace App\Interfaces;

use App\Entity\Client;

/**
 * Interface ClientInterface
 * @package App\Interfaces
 */
interface ClientInterface
{
    /**
     * @return Client
     */
    public function getClient(): ?Client;
}
