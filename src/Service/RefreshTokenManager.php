<?php

namespace App\Service;

use Doctrine\Persistence\ObjectManager;

class RefreshTokenManager extends \Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenManager
{
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);
        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }
}
