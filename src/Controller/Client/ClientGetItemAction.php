<?php

namespace App\Controller\Client;

use App\Entity\Client;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientGetItemAction
{
    public function __invoke(UserInterface $client): ?Client
    {
        if ($client instanceof Client) {
            return $client;
        }

        return null;
    }
}
