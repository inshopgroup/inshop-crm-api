<?php

namespace App\EventListener\Logger;

use App\Entity\History;
use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\PersistentCollection;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use JsonException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function get_class;
use function in_array;
use function is_object;
use function method_exists;

/**
 * Class EntityLoggerSubscriber
 * @package App\EventListener
 */
class EntityLoggerSubscriber implements EventSubscriber
{
    /**
     * @var TokenStorageInterface
     */
    protected TokenStorageInterface $tokenStorage;

    /**
     * @var array
     */
    protected array $logs = [];

    /**
     * @var bool
     */
    private bool $isDisabled = false;

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            'preUpdate',
            'postPersist',
            'preRemove',
            'postFlush',
        ];
    }

    /**
     * @var array
     */
    protected static array $disabledEntities = [
        EntityLogger::class,
        History::class,
        RefreshToken::class,
    ];

    /**
     * @var array
     */
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

    /**
     * EntityLoggerSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws ReflectionException
     * @throws JsonException
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        if ($this->isDisabled() === false) {
            $this->calculateChanges($args->getEntityManager(), $args->getEntity(), 'update');
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws ReflectionException
     * @throws JsonException
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        if ($this->isDisabled() === false) {
            $this->calculateChanges($args->getEntityManager(), $args->getEntity(), 'create');
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        if ($this->isDisabled() === false) {
            $entity = $args->getEntity();
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
    }

    /**
     * @param PostFlushEventArgs $args
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!empty($this->logs) && $this->isDisabled() === false) {
            $em = $args->getEntityManager();

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

    /**
     * @return string
     */
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

    /**
     * @param $object
     * @return string
     */
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

    /**
     * @param array $collection
     * @return string
     */
    protected function getValueForRelations(array $collection): string
    {
        return implode(
            ', ',
            array_map(
                function ($entity)
                {
                    return $this->guessObjectName($entity);
                },
                $collection
            )
        );
    }

    /**
     * @param EntityManagerInterface $em
     * @param $entity
     * @param string $action
     * @throws ReflectionException
     * @throws JsonException
     */
    protected function calculateChanges(EntityManagerInterface $em, $entity, string $action): void
    {
        $entityClassName = get_class($entity);

        if (in_array($entityClassName, self::$disabledEntities, true)) {
            return;
        }

        $uow = $em->getUnitOfWork();

        $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)), $entity);
        $changes = $uow->getEntityChangeSet($entity);

        $annotationReader = new AnnotationReader();

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
                $annotations = $annotationReader->getPropertyAnnotations($reflectionClass->getProperty($attributeName));
                $type = $this->getColumnTypeFromAnnotations($annotations);
            }

            [
                $oldValueRaw,
                $newValueRaw
            ] = [...$change];

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

    /**
     * @param array $annotations
     * @return null|string
     */
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

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    /**
     * @param bool $isDisabled
     */
    public function setIsDisabled(bool $isDisabled): void
    {
        $this->isDisabled = $isDisabled;
    }
}
