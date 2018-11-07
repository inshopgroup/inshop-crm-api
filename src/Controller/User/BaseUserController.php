<?php

namespace App\Controller\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class BaseUserController
 * @package App\Controller
 */
class BaseUserController extends Controller
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
     * @param User $data
     * @return User
     */
    protected function encodePassword(User $data): User
    {
        if (!empty($data->getPlainPassword())) {
            $encoded = $this->encoder->encodePassword($data, $data->getPlainPassword());
            $data->setPassword($encoded);
        }

        return $data;
    }
}
