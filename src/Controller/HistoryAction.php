<?php

namespace App\Controller;

use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class HistoryAction
 * @package App\Controller
 */
class HistoryAction extends Controller
{
    /**
     * @IsGranted("ROLE_OTHER_HISTORY")
     * @Route("/history/{entityClass}/{id}")
     * @param string $entityClass
     * @param int $id
     * @return JsonResponse
     */
    public function indexAction(string $entityClass, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('\\App\\Entity\\' . $entityClass)->find($id);

        /** @var LogEntryRepository $repo */
        $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $logs = $repo->getLogEntries($entity);

        /** @var Serializer $serializer */
        $serializer = $this->container->get('jms_serializer');

        return new JsonResponse(
            json_decode($serializer->serialize($logs,'json'))
        );
    }
}
