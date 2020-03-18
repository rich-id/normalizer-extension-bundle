<?php declare(strict_types=1);

namespace Tests\RichCongress\NormalizerExtensionBundle\Resources;

use RichCongress\Bundle\UnitBundle\Kernel\DefaultTestKernel;
use RichCongress\NormalizerExtensionBundle\RichCongressNormalizerExtensionBundle;

/**
 * Class TestKernel
 *
 * @package   Tests\RichCongress\NormalizerExtensionBundle\Resources
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class TestKernel extends DefaultTestKernel
{
    /**
     * @return array
     */
    public function registerBundles(): array
    {
        $bundles = parent::registerBundles();
        $bundles[] = new RichCongressNormalizerExtensionBundle();

        return $bundles;
    }

    /**
     * @return string|null
     */
    public function getConfigurationDir(): ?string
    {
        return $this->getProjectDir() . '/Tests/Resources/config';
    }
}
