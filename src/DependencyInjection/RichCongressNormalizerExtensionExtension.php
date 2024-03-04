<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\DependencyInjection;

use RichCongress\BundleToolbox\Configuration\AbstractExtension;
use RichCongress\NormalizerExtensionBundle\DependencyInjection\CompilerPass\SerializerPass;
use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\NormalizerExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class RichCongressNormalizerExtensionExtension.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class RichCongressNormalizerExtensionExtension extends AbstractExtension
{
    /** @param array<string, mixed> $configs */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->registerForAutoconfiguration(NormalizerExtensionInterface::class)
            ->addTag(SerializerPass::NORMALIZER_EXTENSION_TAG, ['priority' => 50]);
    }
}
