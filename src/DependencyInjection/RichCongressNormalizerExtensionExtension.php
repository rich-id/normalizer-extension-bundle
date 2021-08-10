<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\DependencyInjection;

use RichCongress\BundleToolbox\Configuration\AbstractExtension;
use RichCongress\NormalizerExtensionBundle\DependencyInjection\CompilerPass\SerializerPass;
use RichCongress\NormalizerExtensionBundle\Serializer\Handler\CircularReferenceHandler;
use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\NormalizerExtensionInterface;
use RichCongress\NormalizerExtensionBundle\Serializer\Serializer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class RichCongressNormalizerExtensionExtension.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class RichCongressNormalizerExtensionExtension extends AbstractExtension
{
    public const SERIALIZER_SERVICE = 'rich_congress.serializer';

    /** @param array<string, mixed> $configs */
    public function load(array $configs, ContainerBuilder $container): void
    {
        self::autoconfigure($container);
        self::configureSerializer($container);
        self::configureCircularReferenceHandler($container);
    }

    protected static function autoconfigure(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(NormalizerExtensionInterface::class)
            ->addTag(SerializerPass::NORMALIZER_EXTENSION_TAG, ['priority' => 50]);
    }

    protected static function configureSerializer(ContainerBuilder $container): void
    {
        $definition = new Definition(Serializer::class);
        $definition->setAutowired(true);
        $definition->setDecoratedService('serializer');
        $definition->setArguments([[], [], []]);
        $container->setDefinition(static::SERIALIZER_SERVICE, $definition);
    }

    protected static function configureCircularReferenceHandler(ContainerBuilder $container): void
    {
        $definition = new Definition(CircularReferenceHandler::class);
        $definition->setAutowired(true);
        $definition->setPublic(true);
        $container->setDefinition(CircularReferenceHandler::class, $definition);
    }
}
