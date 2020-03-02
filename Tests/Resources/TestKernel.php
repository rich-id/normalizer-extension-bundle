<?php declare(strict_types=1);

namespace Tests\RichCongress\NormalizerBundle\Resources;

use RichCongress\Bundle\UnitBundle\Kernel\DefaultTestKernel;
use RichCongress\NormalizerBundle\RichCongressNormalizerBundle;

/**
 * Class TestKernel
 *
 * @package   Tests\RichCongress\NormalizerBundle\Resources
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
        $bundles[] = new RichCongressNormalizerBundle();

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
