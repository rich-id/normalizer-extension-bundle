<?php declare(strict_types=1);

namespace RichCongress\NormalizerBundle\Serializer\Normalizer\Extension;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use RichCongress\NormalizerBundle\Serializer\Annotation\VirtualProperty;

/**
 * Class VirtualPropertyNormalizerExtension
 *
 * @package   RichCongress\NormalizerBundle\Serializer\Normalizer\Extension
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class VirtualPropertyNormalizerExtension implements NormalizerExtensionInterface
{
    /**
     * @param mixed                                         $object
     * @param array|string|int|float|bool|\ArrayObject|null $normalizedData
     * @param string|null                                   $format
     * @param array                                         $context
     *
     * @return array|string|int|float|bool|\ArrayObject|null
     *
     * @throws \ReflectionException
     * @throws AnnotationException
     */
    public function extends($object, $normalizedData, string $format = null, array $context = [])
    {
        $contextGroups = (array) $context['groups'] ?? [];

        /** @var VirtualProperty $virtualProperty */
        foreach ($this->getVirtualProperties($object) as $virtualProperty) {
            $callback = [$object, $virtualProperty->method];
            $groups = (array)$virtualProperty->groups ?? [];

            if (static::hasCommonEntry($contextGroups, $groups)) {
                $normalizedData[$virtualProperty->name] = $callback();
            }
        }

        return $normalizedData;
    }

    /**
     * @param mixed                                         $object
     * @param array|string|int|float|bool|\ArrayObject|null $normalizedData
     * @param string|null                                   $format
     * @param array                                         $context
     *
     * @return bool
     *
     * @throws \ReflectionException
     * @throws AnnotationException
     */
    public function supportsExtension($object, $normalizedData, string $format = null, array $context = []): bool
    {
        if (!\is_object($object) || !\is_array($normalizedData)) {
            return false;
        }

        $virtualProperties = $this->getVirtualProperties($object);

        return $virtualProperties->current() !== null;
    }

    /**
     * @param $object
     *
     * @return \Generator|VirtualProperty[]
     *
     * @throws \ReflectionException
     * @throws AnnotationException
     */
    protected function getVirtualProperties($object): \Generator
    {
        $reflectionClass = new \ReflectionClass($object);
        $annotationReader = new AnnotationReader();

        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            /** @var VirtualProperty|null $virtualProperty */
            $virtualProperty = $annotationReader->getMethodAnnotation($reflectionMethod, VirtualProperty::class);

            if ($virtualProperty !== null) {
                $virtualProperty->method = $reflectionMethod->getName();

                yield $virtualProperty;
            }
        }
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return bool
     */
    protected static function hasCommonEntry(array $array1, array $array2): bool
    {
        $merged = array_merge($array1, $array2);

        return count($merged) > count(array_unique($merged));
    }
}
