<?php

namespace App\Controller\History;

use App\Repository\HistoryRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UserPutItemController
 * @package App\Controller\User
 */
class HistoryGetEntityCollectionAction
{
    /**
     * @param EntityManagerInterface $em
     * @param HistoryRepository $historyRepository
     * @param string $entity
     * @param int $entityId
     * @return array
     */
    public function __invoke(EntityManagerInterface $em, HistoryRepository $historyRepository, string $entity, int $entityId): array
    {
        $entity = $em->getRepository('\\App\\Entity\\' . $entity)->find($entityId);

        return $historyRepository->getLogEntries($entity);
    }
}
