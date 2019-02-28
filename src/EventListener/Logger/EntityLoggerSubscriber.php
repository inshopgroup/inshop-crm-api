<?php

namespace App\EventListener\Logger;

use App\Entity\User;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Loggable\Entity\LogEntry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class EntityLoggerSubscriber
 * @package App\EventListener
 */
class EntityLoggerSubscriber implements EventSubscriber
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var array
     */
    protected $logs = [];

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
    protected static $disabledEntities = [
        EntityLogger::class,
        LogEntry::class,
    ];

    /**
     * @var array
     */
    protected static $disabledAttributes = [
        'createdAt',
        'updatedAt',
        'deletedAt',
        'createdBy',
        'updatedBy',
        'username',
        'password'
    ];

    /**
     * EntityLoggerSubscriber constructor.
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->calculateChanges($args->getEntityManager(), $args->getEntity(), 'update');
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->calculateChanges($args->getEntityManager(), $args->getEntity(), 'create');
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $entityClassName = \get_class($entity);

        if (\in_array($entityClassName, self::$disabledEntities, true)) {
            return;
        }

        $entityLogger = new EntityLogger();
        $entityLogger->setEntity($entity);
        $entityLogger->setAction('remove');
        $entityLogger->addChange('name', $this->guessObjectName($entity));

        $this->logs[] = $entityLogger;
    }

    /**
     * @param PostFlushEventArgs $args
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!empty($this->logs)) {
            $em = $args->getEntityManager();

            /** @var EntityLogger $entityLogger */
            foreach ($this->logs as $entityLogger) {
                if ($entityLogger->getChanges()) {
                    $logEntry = new LogEntry();
                    $logEntry->setAction($entityLogger->getAction());
                    $logEntry->setUsername($this->getUsername());
                    $logEntry->setData($entityLogger->getChanges());
                    $logEntry->setObjectClass(\get_class($entityLogger->getEntity()));
                    $logEntry->setObjectId($entityLogger->getEntity()->getId());
                    $logEntry->setLoggedAt();
                    $logEntry->setVersion((new \DateTime())->getTimestamp());

                    $em->persist($logEntry);
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

            if ($user instanceof User) {
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
    protected function guessObjectName($object): string
    {
        if (\is_object($object) && \method_exists($object, 'getId')) {
            if (\method_exists($object, '__toString')) {
                return sprintf('%s (ID: %d)', $object->__toString(), $object->getId());
            }

            if (\method_exists($object, 'getName')) {
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
                function ($entity) {
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
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    protected function calculateChanges(EntityManagerInterface $em, $entity, string $action): void
    {
        $entityClassName = \get_class($entity);

        if (\in_array($entityClassName, self::$disabledEntities, true)) {
            return;
        }

        $uow = $em->getUnitOfWork();

        $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(\get_class($entity)), $entity);
        $changes = $uow->getEntityChangeSet($entity);

        $annotationReader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($entity);

        $entityLogger = new EntityLogger();
        $entityLogger->setEntity($entity);
        $entityLogger->setAction($action);

        foreach ($changes as $attributeName => $change) {
            if (\in_array($attributeName, self::$disabledAttributes, true)) {
                continue;
            }

            $type = null;
            if ($reflectionClass->hasProperty($attributeName)) {
                $annotations = $annotationReader->getPropertyAnnotations($reflectionClass->getProperty($attributeName));
                $type = $this->getColumnTypeFromAnnotations($annotations);
            }

            $oldValueRaw = $change[0];
            $newValueRaw = $change[1];

            switch ($type) {
                case 'boolean':
                    $oldValue = $oldValueRaw ? 'yes' : 'no';
                    $newValue = $newValueRaw ? 'yes' : 'no';
                    break;

                case 'date':

                    $oldValue = $oldValueRaw && $oldValueRaw instanceof \DateTime ? $oldValueRaw->format('Y-m-d H:i:s') : '';
                    $newValue = $newValueRaw && $newValueRaw instanceof \DateTime ? $newValueRaw->format('Y-m-d H:i:s') : '';
                    break;

                case 'string':
                case 'text':
                case 'integer':
                    $oldValue = $oldValueRaw;
                    $newValue = $newValueRaw;
                    break;

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
            if (\in_array($attributeName, self::$disabledAttributes, true)) {
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
            switch (\get_class($annotation)) {
                case Column::class:
                    return $annotation->type;
                    break;

                case ManyToOne::class:
                    return ManyToOne::class;
                    break;

                default:
                    return null;
            }
        }

        return null;
    }
}
