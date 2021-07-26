<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Serializer\Normalizer;

use RichCongress\NormalizerExtensionBundle\Exception\AttributeNotFoundException;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Entity\DummyEntity;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Serializer\Normalizer\Extension\DummyNormalizerExtension;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\TestCase\NormalizerExtensionTestCase;
use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * Class AbstractObjectNormalizerExtensionExtensionTest.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 *
 * @covers \RichCongress\NormalizerExtensionBundle\Serializer\Serializer
 * @covers \RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension
 * @covers \RichCongress\NormalizerExtensionBundle\Exception\AttributeNotFoundException
 *
 * @TestConfig("container")
 */
class AbstractObjectNormalizerExtensionExtensionTest extends NormalizerExtensionTestCase
{
    /** @var DummyNormalizerExtension */
    protected $normalizerExtension;

    /** @throws ExceptionInterface */
    public function testNormalizeDummyEntitySuccessfully(): void
    {
        $entity = new DummyEntity();
        $entity->booleanValue = false;

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
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

        self::assertFalse($data['booleanValue']);
        self::assertEquals('content', $data['normalizerField']);
        self::assertEquals(['yes'], $data['normalizerAttribute']);
        self::assertEquals('fallback', $data['normalizerAttributeWithDefault']);
        self::assertTrue($data['isEntityBoolean']);
    }

    /** @throws ExceptionInterface */
    public function testNormalizeDummyEntityWithSkip(): void
    {
        $entity = new DummyEntity();
        $entity->booleanValue = true;

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
            $entity,
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_boolean_value',
                    'dummy_entity_normalizer_field',
                ],
            ]
        );

        self::assertArrayHasKey('booleanValue', $data);
        self::assertArrayNotHasKey('normalizerField', $data);

        self::assertTrue($data['booleanValue']);
    }

    /** @throws ExceptionInterface */
    public function testNormalizeWithAttributeException(): void
    {
        $entity = new DummyEntity();

        $this->expectException(AttributeNotFoundException::class);

        $this->normalizer->normalize(
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

    /** @throws ExceptionInterface */
    public function testNormalizeWithNoFunction(): void
    {
        $entity = new DummyEntity();

        $this->expectException(\LogicException::class);

        $this->normalizer->normalize(
            $entity,
            'json',
            [
                'groups' => [
                    'dummy_entity_no_functions',
                ],
            ]
        );
    }

    public function testNormalizeNull(): void
    {
        $data = $this->normalize(null);

        self::assertNull($data);
    }
}
