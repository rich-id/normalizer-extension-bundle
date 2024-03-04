<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\Serializer\Normalizer\Extension;

use RichCongress\NormalizerExtensionBundle\Serializer\Batch\DeferredValue;
use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Model\DummyModelWithId;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Serializer\Batch\DummyBatch;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class DummyWithIdNormalizerExtension extends AbstractObjectNormalizerExtension
{
    public static $objectClass = DummyModelWithId::class;
    public static $contextPrefix = 'dummy_entity_with_id_';

    /** @var DummyBatch */
    protected $dummyBatch;

    /** @var NormalizerInterface */
    protected $normalizer;

    #[Required]
    public function setDummyBatch(DummyBatch $dummyBatch): self
    {
        $this->dummyBatch = $dummyBatch;

        return $this;
    }

    #[Required]
    public function setNormalizer(NormalizerInterface $normalizer): self
    {
        $this->normalizer = $normalizer;

        return $this;
    }

    /** @return array<string, string> */
    public static function getSupportedGroups(): array
    {
        return [
            'batched_value'                   => 'batchedValue',
            'dummy_entity_catch_exception'    => 'dummyEntityCatchException',
            'dummy_entity_no_catch_exception' => 'dummyEntityNoCatchException',
        ];
    }

    public function getBatchedValue(DummyModelWithId $entity): DeferredValue
    {
        return $this->dummyBatch->defer($entity->getId());
    }

    /** @return array<string, mixed>|string|int|float|bool|\ArrayObject<string, mixed>|DeferredValue|null */
    public function getDummyEntityCatchException(DummyModelWithId $entity)
    {
        try {
            return $this->normalizer->normalize($entity->getDummyEntity(), null, $this->context);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** @return array<string, mixed>|string|int|float|bool|\ArrayObject<string, mixed>|DeferredValue|null */
    public function getDummyEntityNoCatchException(DummyModelWithId $entity)
    {
        return $this->normalizer->normalize($entity->getDummyEntity(), null, $this->context);
    }
}
