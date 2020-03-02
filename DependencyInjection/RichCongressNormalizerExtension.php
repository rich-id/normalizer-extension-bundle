<?php

namespace RichCongress\NormalizerBundle\DependencyInjection;

use RichCongress\NormalizerBundle\DependencyInjection\CompilerPass\SerializerPass;
use RichCongress\NormalizerBundle\Serializer\Normalizer\Extension\NormalizerExtensionInterface;
use RichCongress\NormalizerBundle\Serializer\NameConverter\VirtualPropertyNameConverter;
use RichCongress\NormalizerBundle\Serializer\Serializer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RichCongressNormalizerExtension
 *
 * @package   RichCongress\NormalizerBundle\DependencyInjection
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class RichCongressNormalizerExtension extends Extension
{
    public const SERIALIZER_SERVICE = 'rich_congress.serializer';

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @return void
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $bundleConfig = $this->processConfiguration($configuration, $configs);

        self::autoconfigure($container);
        self::configureSerializer($container);
        self::configureVirtualProperty($container, $bundleConfig);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    protected static function autoconfigure(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(NormalizerExtensionInterface::class)
            ->addTag(SerializerPass::NORMALIZER_EXTENSION_TAG, ['priority' => 50]);
    }

    /**
     * @param ContainerBuilder $containerBuilder
     *
     * @return void
     */
    protected static function configureSerializer(ContainerBuilder $containerBuilder): void
    {
        $definition = new Definition(Serializer::class);
        $definition->setAutowired(true);
        $definition->setDecoratedService('serializer');
        $definition->setArguments([[], [], []]);
        $containerBuilder->setDefinition(static::SERIALIZER_SERVICE, $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $bundleConfig
     *
     * @return void
     */
    protected static function configureVirtualProperty(ContainerBuilder $container, array $bundleConfig): void
    {
        if (!$bundleConfig['virtual_property']) {
            return;
        }

        $definition = new Definition(VirtualPropertyNameConverter::class);
        $definition->setAutowired(true);
        $definition->setDecoratedService('serializer.name_converter.metadata_aware');
        $definition->setArgument('$innerNameConverter', new Reference('serializer.name_converter.virtual_property.inner'));
        $container->setDefinition('serializer.name_converter.virtual_property', $definition);
    }
}
