<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BaseUserController
 * @package App\Controller\User
 */
abstract class BaseUserController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    /**
     * BaseController constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
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
            $encoded = $this->encoder->encodePassword($data, $data->getPlainPassword());
            $data->setPassword($encoded);
        }

        return $data;
    }
}
