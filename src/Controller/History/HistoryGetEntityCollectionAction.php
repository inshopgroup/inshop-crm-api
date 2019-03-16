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
     * @param int $id
     * @return array
     */
    public function __invoke(EntityManagerInterface $em, HistoryRepository $historyRepository, string $entity, int $id): array
    {
        $entity = $em->getRepository('\\App\\Entity\\' . $entity)->find($id);

        return $historyRepository->getLogEntries($entity);
    }
}
