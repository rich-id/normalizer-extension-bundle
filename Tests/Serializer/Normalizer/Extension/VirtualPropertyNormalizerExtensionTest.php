<?php declare(strict_types=1);

namespace Tests\RichCongress\NormalizerBundle\Serializer\Normalizer;

use RichCongress\Bundle\UnitBundle\TestConfiguration\Annotation\WithContainer;
use RichCongress\NormalizerBundle\Serializer\Normalizer\Extension\VirtualPropertyNormalizerExtension;
use Tests\RichCongress\NormalizerBundle\Resources\Entity\DummyEntity;
use Tests\RichCongress\NormalizerBundle\Resources\TestCase\NormalizerExtensionTestCase;

/**
 * Class VirtualPropertyNormalizerExtensionTest
 *
 * @package   Tests\RichCongress\NormalizerBundle\Serializer\Normalizer
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 *
 * @covers \RichCongress\NormalizerBundle\Serializer\Serializer
 * @covers \RichCongress\NormalizerBundle\Serializer\Normalizer\Extension\VirtualPropertyNormalizerExtension
 */
class VirtualPropertyNormalizerExtensionTest extends NormalizerExtensionTestCase
{
    /**
     * @return void
     */
    public function beforeTest(): void
    {
        $this->normalizerExtension = new VirtualPropertyNormalizerExtension();
    }

    /**
     * @return void
     */
    public function testHasVirtualProperty(): void
    {
        $entity = new DummyEntity();

        $result = $this->normalize($entity, [
            'dummy_entity_has_virtual_property',
            'dummy_entity_boolean_value',
        ]);

        self::assertArrayHasKey('booleanValue', $result);
        self::assertArrayHasKey('hasVirtualProperty', $result);
    }

    /**
     * @WithContainer()
     *
     * @return void
     */
    public function testHasVirtualPropertyFromContainer(): void
    {
        $entity = new DummyEntity();

        $result = $this->normalize($entity, [
            'dummy_entity_has_virtual_property',
            'dummy_entity_boolean_value',
        ]);

        self::assertArrayHasKey('booleanValue', $result);
        self::assertArrayHasKey('hasVirtualProperty', $result);
        self::assertArrayNotHasKey('virtualProperty', $result);
        self::assertArrayNotHasKey('entityBoolean', $result);
    }

    /**
     * @return void
     */
    public function testNotSupportNotObjectOrNotNormalizedArray(): void
    {
        self::assertFalse(
            $this->normalizerExtension->supportsExtension(null, [])
        );

        self::assertFalse(
            $this->normalizerExtension->supportsExtension(new DummyEntity(), 'not_array')
        );
    }
}
