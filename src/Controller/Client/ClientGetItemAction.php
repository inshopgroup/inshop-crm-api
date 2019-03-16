<?php

namespace App\Controller\Client;

use App\Entity\Client;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ClientGetItemAction
 * @package App\Controller\User
 */
class ClientGetItemAction
{
    /**
     * @param UserInterface $client
     * @return Client|null
     */
    public function __invoke(UserInterface $client): ?Client
    {
        if ($client instanceof Client) {
            return $client;
        }

        return null;
    }
}
