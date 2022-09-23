<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class BaseUserController extends AbstractController
{
    protected UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    protected function encodePassword(UserInterface $data): UserInterface
    {
        if (!empty($data->getPlainPassword())) {
            $encoded = $this->encoder->hashPassword($data, $data->getPlainPassword());
            $data->setPassword($encoded);
        }

        return $data;
    }
}
