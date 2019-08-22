<?php

namespace App\EventListener;

use App\Entity\Task;
use App\Service\GoogleClient;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class TaskListener
 * @package App\EventListener
 */
class TaskListener
{
    /**
     * @var GoogleClient
     */
    protected $googleClient;

    /**
     * TaskListener constructor.
     * @param GoogleClient $googleClient
     */
    public function __construct(GoogleClient $googleClient)
    {
        $this->googleClient = $googleClient;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        try {
            $entity = $eventArgs->getObject();

            if ($entity instanceof Task) {
                $user = $entity->getAssignee();

                if ($user) {
                    if (!$user->getIsGoogleSyncEnabled() || !$user->getGoogleCalendarId()) {
                        return;
                    }

                    $this->googleClient->init($user);
                    $this->googleClient->insertEvent($entity, $user);
                }
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        try {
            $entity = $eventArgs->getObject();

            if ($entity instanceof Task) {
                $entityManager = $eventArgs->getEntityManager();
                $unitOfWork = $entityManager->getUnitOfWork();

                $changes = $unitOfWork->getEntityChangeSet($entity);

                if (array_key_exists('assignee', $changes)) {
                    $this->googleClient->deleteEvent($entity, $changes['assignee'][0]);
                    $this->googleClient->insertEvent($entity, $changes['assignee'][1]);
                } else {
                    $this->googleClient->updateEvent($entity, $entity->getAssignee());
                }
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs): void
    {
        try {
            $entity = $eventArgs->getObject();

            if ($entity instanceof Task) {
                $user = $entity->getAssignee();

                if ($user) {
                    if (!$user->getIsGoogleSyncEnabled() || !$user->getGoogleCalendarId()) {
                        return;
                    }

                    $this->googleClient->init($user);
                    $this->googleClient->deleteEvent($entity, $user);
                }
            }
        } catch (\Exception $e) {

        }
    }
}
