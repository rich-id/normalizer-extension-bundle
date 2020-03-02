<?php declare(strict_types=1);

namespace Tests\RichCongress\NormalizerBundle\Serializer\Normalizer;

use RichCongress\Bundle\UnitBundle\TestConfiguration\Annotation\WithContainer;
use RichCongress\NormalizerBundle\Exception\AttributeNotFoundException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Tests\RichCongress\NormalizerBundle\Resources\Entity\DummyEntity;
use Tests\RichCongress\NormalizerBundle\Resources\Serializer\Normalizer\Extension\DummyNormalizerExtension;
use Tests\RichCongress\NormalizerBundle\Resources\TestCase\NormalizerExtensionTestCase;

/**
 * Class AbstractObjectNormalizerExtensionExtensionTest
 *
 * @package   Tests\RichCongress\NormalizerBundle\Serializer\Normalizer
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 *
 * @covers \RichCongress\NormalizerBundle\Serializer\Serializer
 * @covers \RichCongress\NormalizerBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension
 * @covers \RichCongress\NormalizerBundle\Exception\AttributeNotFoundException
 * @WithContainer
 */
class AbstractObjectNormalizerExtensionExtensionTest extends NormalizerExtensionTestCase
{
    /**
     * @var DummyNormalizerExtension
     */
    protected $normalizerExtension;

    /**
     * @return void
     *
     * @throws ExceptionInterface
     */
    public function testNormalizeDummyEntitySuccessfully(): void
    {
        $entity = new DummyEntity();
        $entity->booleanValue = true;

        $data = $this->serializer->normalize(
            $entity,
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_boolean_value',
                    'dummy_entity_normalizer_field',
                    'dummy_entity_normalizer_attribute',
                    'dummy_entity_normalizer_attribute_with_default',
                    'dummy_entity_entity_boolean',
                ],
            ]
        );

        self::assertArrayHasKey('booleanValue', $data);
        self::assertArrayHasKey('normalizerField', $data);
        self::assertArrayHasKey('normalizerAttribute', $data);
        self::assertArrayHasKey('normalizerAttributeWithDefault', $data);
        self::assertArrayHasKey('isEntityBoolean', $data);

        self::assertTrue($data['booleanValue']);
        self::assertEquals('content', $data['normalizerField']);
        self::assertEquals(['yes'], $data['normalizerAttribute']);
        self::assertEquals('fallback', $data['normalizerAttributeWithDefault']);
        self::assertTrue($data['isEntityBoolean']);
    }

    /**
     * @return void
     * @throws ExceptionInterface
     */
    public function testNormalizeWithAttributeException(): void
    {
        $entity = new DummyEntity();

        $this->expectException(AttributeNotFoundException::class);

        $this->serializer->normalize(
            $entity,
            'json',
            [
                'attribute' => ['yes'],
                'groups'    => [
                    'dummy_entity_normalizer_bad_attribute',
                ],
            ]
        );
    }

    /**
     * @return void
     * @throws ExceptionInterface
     */
    public function testNormalizeWithNoFunction(): void
    {
        $entity = new DummyEntity();

        $this->expectException(\LogicException::class);

        $this->serializer->normalize(
            $entity,
            'json',
            [
                'groups'    => [
                    'dummy_entity_no_functions',
                ],
            ]
        );
    }

    /**
     * @return void
     */
    public function testNormalizeNull(): void
    {
        $data = $this->normalize(null);

        self::assertNull($data);
    }
}
