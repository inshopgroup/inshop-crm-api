<?php

namespace App\Security;

use App\Entity\Client;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Client) {
            return;
        }

        if (!$user->getIsActive()) {
            throw new LockedException('Account is locked');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
