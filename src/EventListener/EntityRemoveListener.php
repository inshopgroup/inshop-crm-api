<?php

namespace App\EventListener;

use App\Exception\EntityRemoveException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityRemoveListener
{
    public function preRemove(LifecycleEventArgs $args): void
    {
        $em = $args->getObjectManager();
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
