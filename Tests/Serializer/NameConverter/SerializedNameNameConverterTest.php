<?php declare(strict_types=1);

namespace RichCongress\NormalizerBundle\Tests\Serializer\NameConverter;

use Doctrine\Common\Annotations\AnnotationException;
use RichCongress\Bundle\UnitBundle\TestCase\TestCase;
use RichCongress\NormalizerBundle\Serializer\NameConverter\SerializedNameNameConverter;
use RichCongress\NormalizerBundle\Tests\Resources\Serializer\NameConverter\DummyNameConverter;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Tests\RichCongress\NormalizerBundle\Resources\Entity\DummyEntity;

/**
 * Class SerializedNameNameConverterTest
 *
 * @package   RichCongress\NormalizerBundle\Tests\Serializer\NameConverter
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 *
 * @covers \RichCongress\NormalizerBundle\Serializer\NameConverter\SerializedNameNameConverter
 */
class SerializedNameNameConverterTest extends TestCase
{
    /**
     * @var SerializedNameNameConverter
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
        $this->nameConverter = new SerializedNameNameConverter($innerNameConverter);
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
        $nameConverter = new SerializedNameNameConverter($innerNameConverter);
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
    public function testNormalizeNoSerializedName(): void
    {
        $result = $this->nameConverter->normalize('isEntityBoolean', DummyEntity::class);

        self::assertSame('is_entity_boolean', $result);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testNormalizeWithSerializedNameFoundButNotInContext(): void
    {
        $result = $this->nameConverter->normalize('hasSerializedName', DummyEntity::class);

        self::assertSame('has_serialized_name', $result);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testNormalizeWithSerializedName(): void
    {
        $result = $this->nameConverter->normalize(
            'hasSerializedName',
            DummyEntity::class,
            null,
            [
                AbstractNormalizer::GROUPS => 'dummy_entity_has_virtual_property'
            ]
        );

        self::assertSame('doesItHasSerializedName', $result);
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
        $nameConverter = new SerializedNameNameConverter($innerNameConverter);
        $result = $nameConverter->denormalize('variableName');

        self::assertSame('variableName_empty', $result);
    }
}
