<?php declare(strict_types=1);

namespace Tests\RichCongress\NormalizerExtensionBundle\Resources\Serializer\Normalizer\Extension;

use RichCongress\NormalizerExtensionBundle\Exception\AttributeNotFoundException;
use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension;
use Tests\RichCongress\NormalizerExtensionBundle\Resources\Entity\DummyEntity;

/**
 * Class DummyNormalizerExtension
 *
 * @package   Tests\RichCongress\NormalizerExtensionBundle\Resources\Serializer\Normalizer\Extension
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class DummyNormalizerExtension extends AbstractObjectNormalizerExtension
{
    public static $objectClass = DummyEntity::class;
    public static $contextPrefix = 'dummy_entity_';

    /**
     * @return array
     */
    public static function getSupportedGroups(): array
    {
        return [
            'normalizer_field' => 'normalizerField',
            'normalizer_attribute' => 'normalizerAttribute',
            'normalizer_bad_attribute' => 'normalizerBadAttribute',
            'normalizer_attribute_with_default' => 'normalizerAttributeWithDefault',
            'entity_boolean'   => 'isEntityBoolean',
            'no_functions' => 'noFunction',
        ];
    }

    /**
     * @param DummyEntity $entity
     *
     * @return string
     */
    public function getNormalizerField(DummyEntity $entity): string
    {
        return 'content';
    }

    /**
     * @param DummyEntity $entity
     *
     * @return array
     *
     * @throws AttributeNotFoundException
     */
    public function getNormalizerAttribute(DummyEntity $entity): array
    {
        return $this->getAttribute('attribute');
    }

    /**
     * @param DummyEntity $entity
     *
     * @return array
     *
     * @throws AttributeNotFoundException
     */
    public function getNormalizerBadAttribute(DummyEntity $entity): array
    {
        return $this->getAttribute('bad_attribute');
    }

    /**
     * @param DummyEntity $entity
     *
     * @return string
     */
    public function getNormalizerAttributeWithDefault(DummyEntity $entity): string
    {
        return $this->getAttributeWithDefault('bad_attribute', 'fallback');
    }
}
