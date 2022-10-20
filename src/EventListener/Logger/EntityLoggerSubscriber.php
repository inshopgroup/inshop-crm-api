<?php

namespace App\EventListener\Logger;

use App\Entity\History;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\PersistentCollection;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use ReflectionClass;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function get_class;
use function in_array;
use function is_object;
use function method_exists;

class EntityLoggerSubscriber implements EventSubscriber
{
    protected TokenStorageInterface $tokenStorage;

    protected array $logs = [];

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
            Events::postPersist,
            Events::preRemove,
            Events::postFlush,
        ];
    }

    protected static array $disabledEntities = [
        History::class,
        RefreshToken::class,
    ];

    protected static array $disabledAttributes = [
        'createdAt',
        'updatedAt',
        'deletedAt',
        'createdBy',
        'updatedBy',
        'username',
        'password',
        'token',
        'googleCalendars',
        'googleAccessToken',
    ];

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->calculateChanges($args->getObjectManager(), $args->getObject(), 'update');
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->calculateChanges($args->getObjectManager(), $args->getObject(), 'create');
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $entityClassName = get_class($entity);

        if (in_array($entityClassName, self::$disabledEntities, true)) {
            return;
        }

        $entityLogger = new EntityLogger();
        $entityLogger->setEntity($entity);
        $entityLogger->setAction('remove');
        $entityLogger->addChange('name', $this->guessObjectName($entity));

        $this->logs[] = $entityLogger;
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!empty($this->logs)) {
            $em = $args->getObjectManager();

            /** @var EntityLogger $entityLogger */
            foreach ($this->logs as $entityLogger) {
                if ($entityLogger->getChanges()) {
                    $history = new History();
                    $history->setAction($entityLogger->getAction());
                    $history->setUsername($this->getUsername());
                    $history->setData($entityLogger->getChanges());
                    $history->setObjectClass(get_class($entityLogger->getEntity()));
                    $history->setObjectId($entityLogger->getEntity()->getId());
                    $history->setLoggedAt();
                    $history->setVersion((new DateTime())->getTimestamp());

                    $em->persist($history);
                }
            }

            $this->logs = [];
            $em->flush();
        }
    }

    protected function getUsername(): string
    {
        $token = $this->tokenStorage->getToken();

        if ($token) {
            $user = $token->getUser();

            if ($user instanceof UserInterface) {
                return $user->getUsername();
            }

            return $user;
        }

        return 'Unknown';
    }

    protected function guessObjectName($object): ?string
    {
        if (is_object($object) && method_exists($object, 'getId')) {
            if (method_exists($object, '__toString')) {
                return sprintf('%s (ID: %d)', $object->__toString(), $object->getId());
            }

            if (method_exists($object, 'getName')) {
                return sprintf('%s (ID: %d)', $object->getName(), $object->getId());
            }

            return $object->getId();
        }

        return (string) $object;
    }

    protected function getValueForRelations(array $collection): string
    {
        return implode(
            ', ',
            array_map(
                function ($entity): ?string
                {
                    return $this->guessObjectName($entity);
                },
                $collection
            )
        );
    }

    protected function calculateChanges(EntityManagerInterface $em, $entity, string $action): void
    {
        $entityClassName = get_class($entity);

        if (in_array($entityClassName, self::$disabledEntities, true)) {
            return;
        }

        $metadata = $em->getClassMetadata(get_class($entity));

        $uow = $em->getUnitOfWork();
        $uow->recomputeSingleEntityChangeSet($metadata, $entity);

        $changes = $uow->getEntityChangeSet($entity);

        $reflectionClass = new ReflectionClass($entity);

        if ($entity instanceof Proxy) {
            // This gets the real object, the one that the Proxy extends
            $reflectionClass = $reflectionClass->getParentClass();
        }
        $entityLogger = new EntityLogger();
        $entityLogger->setEntity($entity);
        $entityLogger->setAction($action);

        foreach ($changes as $attributeName => $change) {
            if (in_array($attributeName, self::$disabledAttributes, true)) {
                continue;
            }

            $type = null;

            if ($reflectionClass->hasProperty($attributeName)) {
                $type = $metadata->getTypeOfField($attributeName);
            }

            if ($type) {
                [$oldValueRaw, $newValueRaw] = [...$change];

                switch ($type) {
                    case 'boolean':
                        $oldValue = $oldValueRaw ? 'yes' : 'no';
                        $newValue = $newValueRaw ? 'yes' : 'no';
                        break;

                    case 'date':
                        $oldValue = $change[0] ? $change[0]->format('d-m-Y') : '';
                        $newValue = $change[1] ? $change[1]->format('d-m-Y') : '';
                        break;

                    case 'time':
                        $oldValue = $change[0] ? $change[0]->format('H:i:s') : '';
                        $newValue = $change[1] ? $change[1]->format('H:i:s') : '';
                        break;

                    case 'datetime':
                        $oldValue = $change[0] ? $change[0]->format('d-m-Y H:i:s') : '';
                        $newValue = $change[1] ? $change[1]->format('d-m-Y H:i:s') : '';
                        break;

                    case 'string':
                    case 'text':
                    case 'integer':
                        $oldValue = $oldValueRaw;
                        $newValue = $newValueRaw;
                        break;

                    case 'json':
                        $oldValue = json_encode($oldValueRaw, JSON_THROW_ON_ERROR, 512);
                        $newValue = json_encode($newValueRaw, JSON_THROW_ON_ERROR, 512);
                        break;

                    case OneToOne::class:
                    case ManyToOne::class:
                        $oldValue = $this->guessObjectName($oldValueRaw);
                        $newValue = $this->guessObjectName($newValueRaw);
                        break;

                    default:
                        $oldValue = $oldValueRaw;
                        $newValue = $newValueRaw;
                }

                if ($oldValue !== $newValue) {
                    $entityLogger->addChange($attributeName, $newValue);
                }
            }
        }

        /** @var PersistentCollection $col */
        foreach ($uow->getScheduledCollectionUpdates() as $col) {
            if (!($col->getOwner() instanceof $entity) || $col->getOwner()->getId() !== $entity->getId()) {
                continue;
            }

            $attributeName = $col->getMapping()['fieldName'];
            if (in_array($attributeName, self::$disabledAttributes, true)) {
                continue;
            }

            $oldValue = $this->getValueForRelations($col->getSnapshot());
            $newValue = $this->getValueForRelations($col->toArray());

            if ($oldValue !== $newValue) {
                $entityLogger->addChange($attributeName, $newValue);
            }
        }

        $this->logs[] = $entityLogger;
    }

    protected function getColumnTypeFromAnnotations(array $annotations): ?string
    {
        foreach ($annotations as $annotation) {
            switch (get_class($annotation)) {
                case Column::class:
                    return $annotation->type;

                case ManyToOne::class:
                    return ManyToOne::class;

                case OneToOne::class:
                    return OneToOne::class;

                default:
                    return null;
            }
        }

        return null;
    }
}
