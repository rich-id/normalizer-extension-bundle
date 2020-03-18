<?php

namespace RichCongress\NormalizerExtensionBundle;

use RichCongress\NormalizerExtensionBundle\DependencyInjection\CompilerPass\SerializerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RichCongressNormalizerExtensionBundle
 *
 * @package   RichCongress\NormalizerExtensionBundle
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class RichCongressNormalizerExtensionBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SerializerPass());
    }
}
