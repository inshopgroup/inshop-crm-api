<?php

namespace App\Controller;

use App\Service\Elastica\Client\ElasticaClientSearch;
use App\Service\ElasticaPaginator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SearchAction
 * @package App\Controller
 */
class SearchAction
{
    /**
     * @param Request $request
     * @param ElasticaClientSearch $search
     * @return ElasticaPaginator
     */
    public function __invoke(Request $request, ElasticaClientSearch $search): ElasticaPaginator
    {
        return $search->search($request->query->all());
    }
}
