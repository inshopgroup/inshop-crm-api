<?php

namespace App\Service\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Product;
use App\Service\Elastica\Client\ElasticaClientProduct;
use App\Service\ElasticaPaginator;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ProductCollectionDataProvider
 * @package App\Service\DataProvider
 */
final class ProductCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var ElasticaClientProduct
     */
    private ElasticaClientProduct $elastica;

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * ProductCollectionDataProvider constructor.
     * @param ElasticaClientProduct $elastica
     * @param RequestStack $requestStack
     */
    public function __construct(ElasticaClientProduct $elastica, RequestStack $requestStack)
    {
        $this->elastica = $elastica;
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Product::class === $resourceClass && $operationName === 'frontendGet';
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @return ElasticaPaginator
     */
    public function getCollection(string $resourceClass, string $operationName = null): ElasticaPaginator
    {
        $request = $this->requestStack->getCurrentRequest();
        $params = $request ? $request->query->all() : [];

        return $this->elastica->search($params);
    }
}
