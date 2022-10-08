<?php

namespace App\Interfaces;

use App\Entity\Client;

interface ClientInterface
{
    public function getClient(): Client;
}
