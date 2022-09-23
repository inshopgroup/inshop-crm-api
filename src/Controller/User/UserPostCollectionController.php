<?php

namespace App\Controller\User;

use Symfony\Component\Security\Core\User\UserInterface;

class UserPostCollectionController extends BaseUserController
{
    public function __invoke(UserInterface $data): UserInterface
    {
        return $this->encodePassword($data);
    }
}
