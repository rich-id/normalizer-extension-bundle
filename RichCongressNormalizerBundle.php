<?php

namespace RichCongress\NormalizerBundle;

use RichCongress\NormalizerBundle\DependencyInjection\CompilerPass\SerializerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RichCongressNormalizerBundle
 *
 * @package   RichCongress\NormalizerBundle
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class RichCongressNormalizerBundle extends Bundle
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
