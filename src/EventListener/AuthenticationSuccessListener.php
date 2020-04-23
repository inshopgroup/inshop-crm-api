<?php

namespace App\EventListener;

use App\Entity\History;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

/**
 * Class AuthenticationSuccessListener
 * @package App\EventListener
 */
class AuthenticationSuccessListener
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AuthenticationSuccessListener constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     * @throws Exception
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $history = new History();
        $history->setAction('login');
        $history->setUsername($user->getUsername());
        $history->setData([]);
        $history->setObjectClass('');
        $history->setObjectId(null);
        $history->setLoggedAt();
        $history->setVersion((new DateTime())->getTimestamp());

        $this->em->persist($history);
        $this->em->flush();

        $data['roles'] = $user->getRoles();
        $data['language'] = $user->getLanguage()->getCode();

        $event->setData($data);
    }
}
