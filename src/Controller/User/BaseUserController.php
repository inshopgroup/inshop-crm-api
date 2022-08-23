<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BaseUserController
 * @package App\Controller\User
 */
abstract class BaseUserController extends AbstractController
{
    /**
     * @var UserPasswordHasherInterface
     */
    protected UserPasswordHasherInterface $encoder;

    /**
     * BaseController constructor.
     * @param UserPasswordHasherInterface $encoder
     */
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param UserInterface $data
     * @return UserInterface
     */
    protected function encodePassword(UserInterface $data): UserInterface
    {
        if (!empty($data->getPlainPassword())) {
            $encoded = $this->encoder->hashPassword($data, $data->getPlainPassword());
            $data->setPassword($encoded);
        }

        return $data;
    }
}
