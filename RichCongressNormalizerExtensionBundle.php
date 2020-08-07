<?php

namespace RichCongress\NormalizerExtensionBundle;

use RichCongress\BundleToolbox\Configuration\AbstractBundle;
use RichCongress\NormalizerExtensionBundle\DependencyInjection\CompilerPass\SerializerPass;

/**
 * Class RichCongressNormalizerExtensionBundle
 *
 * @package   RichCongress\NormalizerExtensionBundle
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class RichCongressNormalizerExtensionBundle extends AbstractBundle
{
    public const COMPILER_PASSES = [SerializerPass::class];
}
