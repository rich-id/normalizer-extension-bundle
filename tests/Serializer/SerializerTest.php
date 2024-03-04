<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Serializer;

use RichCongress\NormalizerExtensionBundle\Exception\AttributeNotFoundException;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Model\DummyModel;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Model\DummyModelWithId;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Serializer\Batch\DummyBatch;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\TestCase\NormalizerExtensionTestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * Class SerializerTest.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 *
 * @covers \RichCongress\NormalizerExtensionBundle\Serializer\Serializer
 * @covers \RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension
 * @covers \RichCongress\NormalizerExtensionBundle\Exception\AttributeNotFoundException
 */
class SerializerTest extends NormalizerExtensionTestCase
{
    /** @var DummyBatch */
    protected $dummyBatch;

    protected function beforeTest(): void
    {
        /** @var DummyBatch $dummyBatch */
        $dummyBatch = $this->getService(DummyBatch::class);
        $this->dummyBatch = $dummyBatch;
    }

    /** @throws ExceptionInterface */
    public function testNormalizeDummyEntitySuccessfully(): void
    {
        $entity = new DummyModel();
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
        $entity = new DummyModel();
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
        $entity = new DummyModel();

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
        $entity = new DummyModel();

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

    public function testBatchQuery(): void
    {
        self::assertEquals([], $this->dummyBatch->calls);

        $entity1 = new DummyModelWithId();
        $entity1->id = 1;
        $entity2 = new DummyModelWithId();
        $entity2->id = 2;

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
            [$entity1, $entity2],
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_with_id_batched_value',
                ],
            ]
        );

        self::assertEquals([['batchedValue' => 1], ['batchedValue' => 2]], $data);
        self::assertEquals([[1, 2]], $this->dummyBatch->calls);
    }

    public function testBatchMultipleDataWithSameKeyAreResolvedOnlyOnce(): void
    {
        self::assertEquals([], $this->dummyBatch->calls);

        $entity1 = new DummyModelWithId();
        $entity1->id = 1;
        $entity2 = new DummyModelWithId();
        $entity2->id = 1;

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
            [$entity1, $entity2],
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_with_id_batched_value',
                ],
            ]
        );

        self::assertEquals([['batchedValue' => 1], ['batchedValue' => 1]], $data);
        self::assertEquals([[1]], $this->dummyBatch->calls);
    }

    public function testBatchCachesAlreadyResolvedValues(): void
    {
        self::assertEquals([], $this->dummyBatch->calls);

        $entity1 = new DummyModelWithId();
        $entity1->id = 1;
        $entity2 = new DummyModelWithId();
        $entity2->id = 2;

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
            [$entity1],
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_with_id_batched_value',
                ],
            ]
        );

        self::assertEquals([['batchedValue' => 1]], $data);
        self::assertEquals([[1]], $this->dummyBatch->calls);

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
            [$entity1, $entity2],
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_with_id_batched_value',
                ],
            ]
        );

        self::assertEquals([['batchedValue' => 1], ['batchedValue' => 2]], $data);
        self::assertEquals([[1], [2]], $this->dummyBatch->calls);
    }

    public function testBatchHandlesFailedResponses(): void
    {
        self::assertEquals([], $this->dummyBatch->calls);

        $entity1 = new DummyModelWithId();
        $entity1->id = 1;
        $entity2 = new DummyModelWithId();
        $entity2->id = 2;
        $this->dummyBatch->ignoredKey = 2;

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
            [$entity1, $entity2],
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_with_id_batched_value',
                ],
            ]
        );

        self::assertEquals([['batchedValue' => 1], ['batchedValue' => null]], $data);
        self::assertEquals([[1, 2], [2]], $this->dummyBatch->calls);
    }

    public function testBatchCorrectlyTrackRecursionInThePresenceOfExceptionsInTheMiddleOfTheStack(): void
    {
        $entity = new DummyModelWithId();
        $entity->id = 1;
        $entity->dummyEntity = new DummyModel();

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
            $entity,
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_with_id_batched_value',
                    'dummy_entity_with_id_dummy_entity_catch_exception',
                    'dummy_entity_raises_exception',
                ],
            ]
        );

        self::assertEquals(['batchedValue' => 1, 'dummyEntityCatchException' => null], $data);
        self::assertEquals([[1]], $this->dummyBatch->calls);

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
            $entity,
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_with_id_batched_value',
                    'dummy_entity_with_id_dummy_entity_catch_exception',
                    'dummy_entity_raises_exception',
                ],
            ]
        );

        self::assertEquals(['batchedValue' => 1, 'dummyEntityCatchException' => null], $data);
        self::assertEquals([[1]], $this->dummyBatch->calls);
    }

    public function testBatchCorrectlyTrackRecursionInThePresenceOfExceptionsThatBlowsTheWholeStack(): void
    {
        $entity = new DummyModelWithId();
        $entity->id = 1;
        $entity->dummyEntity = new DummyModel();

        try {
            /** @var array<string, mixed> $data */
            $data = $this->normalizer->normalize(
                $entity,
                'json',
                [
                    'attribute'                    => ['yes'],
                    AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                    AbstractNormalizer::GROUPS     => [
                        'dummy_entity_with_id_dummy_entity_no_catch_exception',
                        'dummy_entity_raises_exception',
                    ],
                ]
            );
        } catch (\Throwable $e) {
            $data = null;
        }

        self::assertNull($data);
        self::assertEquals([], $this->dummyBatch->calls);

        /** @var array<string, mixed> $data */
        $data = $this->normalizer->normalize(
            $entity,
            'json',
            [
                'attribute'                    => ['yes'],
                AbstractNormalizer::ATTRIBUTES => ['booleanValue'],
                AbstractNormalizer::GROUPS     => [
                    'dummy_entity_with_id_batched_value',
                ],
            ]
        );

        self::assertEquals(['batchedValue' => 1], $data);
        self::assertEquals([[1]], $this->dummyBatch->calls);
    }
}
