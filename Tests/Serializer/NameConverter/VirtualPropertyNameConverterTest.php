<?php declare(strict_types=1);

namespace RichCongress\NormalizerBundle\Tests\Serializer\NameConverter;

use Doctrine\Common\Annotations\AnnotationException;
use RichCongress\Bundle\UnitBundle\TestCase\TestCase;
use RichCongress\NormalizerBundle\Serializer\NameConverter\VirtualPropertyNameConverter;
use RichCongress\NormalizerBundle\Tests\Resources\Serializer\NameConverter\DummyNameConverter;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Tests\RichCongress\NormalizerBundle\Resources\Entity\DummyEntity;

/**
 * Class VirtualPropertyNameConverterTest
 *
 * @package   RichCongress\NormalizerBundle\Tests\Serializer\NameConverter
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 *
 * @covers \RichCongress\NormalizerBundle\Serializer\NameConverter\VirtualPropertyNameConverter
 */
class VirtualPropertyNameConverterTest extends TestCase
{
    /**
     * @var VirtualPropertyNameConverter
     */
    protected $nameConverter;

    /**
     * @return void
     *
     * @throws AnnotationException
     */
    protected function beforeTest(): void
    {
        $innerNameConverter = new CamelCaseToSnakeCaseNameConverter();
        $this->nameConverter = new VirtualPropertyNameConverter($innerNameConverter);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testNormalizerWithoutClass(): void
    {
        $result = $this->nameConverter->normalize('variableName');

        self::assertSame('variable_name', $result);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws AnnotationException
     */
    public function testNormalizeWithoutClassWithAdvancedInnerNameConverter(): void
    {
        $innerNameConverter = new DummyNameConverter();
        $nameConverter = new VirtualPropertyNameConverter($innerNameConverter);
        $result = $nameConverter->normalize('variableName');

        self::assertSame('variableName_empty', $result);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testNormalizeNoMatchingMethod(): void
    {
        $result = $this->nameConverter->normalize('variableName', DummyEntity::class);

        self::assertSame('variable_name', $result);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testNormalizeNoVirtualProperty(): void
    {
        $result = $this->nameConverter->normalize('isEntityBoolean', DummyEntity::class);

        self::assertSame('is_entity_boolean', $result);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testNormalizeWithVirtualPropertyFoundButNotInContext(): void
    {
        $result = $this->nameConverter->normalize('hasVirtualProperty', DummyEntity::class);

        self::assertSame('has_virtual_property', $result);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testNormalizeWithVirtualProperty(): void
    {
        $result = $this->nameConverter->normalize(
            'hasVirtualProperty',
            DummyEntity::class,
            null,
            [
                AbstractNormalizer::GROUPS => 'dummy_entity_has_virtual_property'
            ]
        );

        self::assertSame('doesItHasVirtualProperty', $result);
    }

    /**
     * @return void
     */
    public function testDenormalize(): void
    {
        $result = $this->nameConverter->denormalize('variable_name');

        self::assertSame('variableName', $result);
    }

    /**
     * @return void
     *
     * @throws AnnotationException
     */
    public function testDenormalizeWithoutClassWithAdvancedInnerNameConverter(): void
    {
        $innerNameConverter = new DummyNameConverter();
        $nameConverter = new VirtualPropertyNameConverter($innerNameConverter);
        $result = $nameConverter->denormalize('variableName');

        self::assertSame('variableName_empty', $result);
    }
}
