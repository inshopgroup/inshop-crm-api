<?php

namespace App\Service\Normalizer;

use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\JsonLd\ContextBuilder;
use ApiPlatform\Core\JsonLd\Serializer\JsonLdContextTrait;
use ApiPlatform\Core\Serializer\ContextTrait;
use App\Service\ElasticaPaginator;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ElasticaCollectionNormalizer
 * @package App\Service\Normalizer
 */
final class ElasticaCollectionNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use ContextTrait;
    use JsonLdContextTrait;
    use NormalizerAwareTrait;

    public const FORMAT = 'jsonld';

    private ContextBuilder $contextBuilder;

    private ResourceClassResolverInterface $resourceClassResolver;

    /**
     * ElasticaCollectionNormalizer constructor.
     * @param ContextBuilder $contextBuilder
     * @param ResourceClassResolverInterface $resourceClassResolver
     */
    public function __construct(ContextBuilder $contextBuilder, ResourceClassResolverInterface $resourceClassResolver)
    {
        $this->contextBuilder = $contextBuilder;
        $this->resourceClassResolver = $resourceClassResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, ?string $format = null): bool
    {
        return self::FORMAT === $format && ($data instanceof ElasticaPaginator);
    }

    /**
     * @param ElasticaPaginator $object
     * @param string|null $format
     * @param array $context
     * @return array|bool|float|int|string
     * @throws ExceptionInterface
     */
    public function normalize(ElasticaPaginator $object, ?string $format = null, array $context = [])
    {
        $resourceClass =
            $this->resourceClassResolver->getResourceClass($object, $context['resource_class'] ?? null, true);
        $data = $this->addJsonLdContext($this->contextBuilder, $resourceClass, $context);
        $context = $this->initContext($resourceClass, $context);

        $data['@type'] = 'hydra:Collection';

        $data['hydra:member'] = [];
        foreach ($object as $obj) {
            $data['hydra:member'][] = $this->normalizer->normalize($obj, $format, $context);
        }

        $data['hydra:totalItems'] = $object->getTotalItems();
        $data['hydra:aggregations'] = $object->getAggregations();

        return $data;
    }

    /**
     * {}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
