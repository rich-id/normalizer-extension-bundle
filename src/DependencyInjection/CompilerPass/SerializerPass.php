<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\DependencyInjection\CompilerPass;

use RichCongress\NormalizerExtensionBundle\DependencyInjection\RichCongressNormalizerExtensionExtension;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SerializerPass extends \Symfony\Component\Serializer\DependencyInjection\SerializerPass
{
    use PriorityTaggedServiceTrait;
    public const NORMALIZER_EXTENSION_TAG = 'serializer.normalizer.extension';

    /** @var string */
    protected $serializerService;

    /** @var string */
    protected $normalizerTag;

    /** @var string */
    protected $encoderTag;

    /** @var string */
    protected $extensionTag;

    public function __construct(
        string $serializerService = RichCongressNormalizerExtensionExtension::SERIALIZER_SERVICE,
        string $normalizerTag = 'serializer.normalizer',
        string $encoderTag = 'serializer.encoder',
        string $extensionTag = self::NORMALIZER_EXTENSION_TAG
    ) {
        parent::__construct($serializerService, $normalizerTag, $encoderTag);

        $this->serializerService = $serializerService;
        $this->normalizerTag = $normalizerTag;
        $this->encoderTag = $encoderTag;
        $this->extensionTag = $extensionTag;
    }

    public function process(ContainerBuilder $container): void
    {
        parent::process($container);

        $extensions = $this->findAndSortTaggedServices($this->extensionTag, $container);

        $serializerDefinition = $container->getDefinition($this->serializerService);
        $serializerDefinition->replaceArgument(2, $extensions);
    }
}
