<?php

namespace App\Controller;

use App\Service\Elastica\Client\ElasticaClientSearch;
use Elastica\ResultSet;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SearchAction
 * @package App\Controller
 */
class SearchAction extends Controller
{
    /**
     * @var ElasticaClientSearch
     */
    protected $search;

    /**
     * SearchAction constructor.
     * @param ElasticaClientSearch $search
     */
    public function __construct(ElasticaClientSearch $search)
    {
        $this->search = $search;
    }

    /**
     * @IsGranted("ROLE_OTHER_SEARCH")
     * @param Request $request
     * @return JsonResponse
     * @Route("/search")
     */
    public function indexAction(Request $request): JsonResponse
    {
        /** @var ResultSet $resultSet */
        $resultSet = $this->search->search($request->query->get('q'));

        /** @var Serializer $serializer */
        $serializer = $this->container->get('jms_serializer');

        return new JsonResponse([
            'count' => $resultSet->count(),
            'documents' => json_decode($serializer->serialize($resultSet->getDocuments(),'json')),
        ]);
    }
}
