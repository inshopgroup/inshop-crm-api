<?php

namespace App\Controller\History;

use App\Repository\HistoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class HistoryGetEntityCollectionAction
{
    public function __invoke(EntityManagerInterface $em, HistoryRepository $historyRepository, string $entity, int $entityId): array
    {
        $entity = $em->getRepository('\\App\\Entity\\' . $entity)->find($entityId);

        return $historyRepository->getLogEntries($entity);
    }
}
