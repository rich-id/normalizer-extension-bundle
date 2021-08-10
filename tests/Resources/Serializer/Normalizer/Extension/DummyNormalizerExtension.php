<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\Serializer\Normalizer\Extension;

use RichCongress\NormalizerExtensionBundle\Exception\AttributeNotFoundException;
use RichCongress\NormalizerExtensionBundle\Exception\SkipSerializationException;
use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Entity\DummyEntity;

/**
 * Class DummyNormalizerExtension.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class DummyNormalizerExtension extends AbstractObjectNormalizerExtension
{
    public static $objectClass = DummyEntity::class;
    public static $contextPrefix = 'dummy_entity_';

    /** @return array<string, string> */
    public static function getSupportedGroups(): array
    {
        return [
            'normalizer_field'                  => 'normalizerField',
            'normalizer_attribute'              => 'normalizerAttribute',
            'normalizer_bad_attribute'          => 'normalizerBadAttribute',
            'normalizer_attribute_with_default' => 'normalizerAttributeWithDefault',
            'entity_boolean'                    => 'isEntityBoolean',
            'no_functions'                      => 'noFunction',
        ];
    }

    /** @throws SkipSerializationException */
    public function getNormalizerField(DummyEntity $entity): string
    {
        if ($entity->booleanValue === true) {
            throw new SkipSerializationException('Skipped');
        }

        return 'content';
    }

    /**
     * @return array<string, mixed>
     *
     * @throws AttributeNotFoundException
     */
    public function getNormalizerAttribute(DummyEntity $entity): array
    {
        return $this->getAttribute('attribute');
    }

    /**
     * @return array<string, mixed>
     *
     * @throws AttributeNotFoundException
     */
    public function getNormalizerBadAttribute(DummyEntity $entity): array
    {
        return $this->getAttribute('bad_attribute');
    }

    public function getNormalizerAttributeWithDefault(DummyEntity $entity): string
    {
        return $this->getAttributeWithDefault('bad_attribute', 'fallback');
    }
}
