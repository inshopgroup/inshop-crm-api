<?php

namespace App\Controller\User;

use App\Entity\User;

/**
 * Class UserPostCollectionController
 * @package App\Controller
 */
class UserPostCollectionController extends BaseUserController
{
    /**
     * @param User $data
     * @return User
     */
    public function __invoke(User $data): User
    {
        return $this->encodePassword($data);
    }
}
