<?php

namespace App\Controller\User;

use App\Entity\User;

/**
 * Class UserPutItemController
 * @package App\Controller
 */
class UserPutItemController extends BaseUserController
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
