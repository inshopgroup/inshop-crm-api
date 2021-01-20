<?php

namespace App\Service\Normalizer;

use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\JsonLd\ContextBuilder;
use ApiPlatform\Core\JsonLd\Serializer\JsonLdContextTrait;
use ApiPlatform\Core\Serializer\ContextTrait;
use stdClass;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ElasticaItemNormalizer
 * @package App\Service\Normalizer
 */
final class ElasticaItemNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use ContextTrait;
    use JsonLdContextTrait;
    use NormalizerAwareTrait;

    /**
     * @var string
     */
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
        return self::FORMAT === $format && ($data instanceof stdClass);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, ?string $format = null, array $context = [])
    {
        $context = $this->initContext('array', $context);

        return $this->normalizer->normalize((array) $object, $format, $context);
    }

    /**
     * {}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
