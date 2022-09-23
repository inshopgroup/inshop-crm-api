<?php

namespace App\Controller\Client;

use App\Controller\User\BaseUserController;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientPutItemController extends BaseUserController
{
    public function __invoke(UserInterface $data): UserInterface
    {
        return $this->encodePassword($data);
    }
}
