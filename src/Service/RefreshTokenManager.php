<?php

namespace App\Service;

use Doctrine\Persistence\ObjectManager;

/**
 * Class RefreshTokenManager
 * @package App\Service
 */
class RefreshTokenManager extends \Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenManager
{
    /**
     * RefreshTokenManager constructor.
     * @param ObjectManager $om
     * @param $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);
        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }
}
