<?php

namespace App\Controller\User;

use Symfony\Component\Security\Core\User\UserInterface;

class UserPutItemController extends BaseUserController
{
    public function __invoke(UserInterface $data): UserInterface
    {
        return $this->encodePassword($data);
    }
}
