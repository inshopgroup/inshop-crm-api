<?php

namespace App\Controller\Client;

use App\Controller\User\BaseUserController;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ClientPutItemController
 * @package App\Controller\User
 */
class ClientPutItemController extends BaseUserController
{
    /**
     * @param UserInterface $data
     * @return UserInterface
     */
    public function __invoke(UserInterface $data): UserInterface
    {
        return $this->encodePassword($data);
    }
}
