<?php

namespace App\Controller\Product;

use App\Service\Elastica\Client\ElasticaClientProduct;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ProductFrontendGetItemAction
 * @package App\Controller\Product
 */
class ProductFrontendGetItemAction
{
    /**
     * @var ElasticaClientProduct
     */
    private $elastica;

    /**
     * ProductFrontendGetItemAction constructor.
     * @param ElasticaClientProduct $elastica
     */
    public function __construct(ElasticaClientProduct $elastica)
    {
        $this->elastica = $elastica;
    }

    /**
     * @param Request $request
     * @return \stdClass
     */
    public function __invoke(Request $request): \stdClass
    {
        $item = $this->elastica->findBySlug($request->get('slug'));

        if (!$item) {
            throw new NotFoundHttpException();
        }

        return (object) $item->toArray()['_source'];
    }
}
