<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\DependencyInjection\CompilerPass;

use RichCongress\NormalizerExtensionBundle\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SerializerPass extends \Symfony\Component\Serializer\DependencyInjection\SerializerPass
{
    use PriorityTaggedServiceTrait;
    public const NORMALIZER_EXTENSION_TAG = 'serializer.normalizer.extension';

    public function process(ContainerBuilder $container): void
    {
        parent::process($container);

        $extensions = $this->findAndSortTaggedServices(self::NORMALIZER_EXTENSION_TAG, $container);

        $serializerDefinition = $container->getDefinition('serializer');
        $serializerDefinition->setClass(Serializer::class);
        $serializerDefinition->addMethodCall('setExtensions', [$extensions]);
    }
}
