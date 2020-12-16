<?php

namespace App\EventListener;

use App\Exception\EntityRemoveException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class EntityRemoveListener
 * @package App\EventListener
 */
class EntityRemoveListener
{
    /**
     * @param LifecycleEventArgs $args
     * @throws EntityRemoveException
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $entity = $args->getObject();

        $associationNames = $em->getClassMetadata(get_class($entity))->getAssociationNames();
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($associationNames as $name) {
            $association = $accessor->getValue($entity, $name);

            if ($association instanceof Collection && $association->count()) {
                throw new EntityRemoveException('Can not remove entity');
            }
        }
    }
}
