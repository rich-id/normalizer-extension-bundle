<?php declare(strict_types=1);

namespace RichCongress\NormalizerBundle\Serializer\NameConverter;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use RichCongress\NormalizerBundle\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Class VirtualPropertyNameConverter
 *
 * @package   RichCongress\NormalizerBundle\Serializer\NameConverter
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class VirtualPropertyNameConverter implements AdvancedNameConverterInterface
{
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var NameConverterInterface
     */
    protected $innerNameConverter;

    /**
     * VirtualPropertyNameConverter constructor.
     *
     * @param NameConverterInterface $innerNameConverter
     *
     * @throws AnnotationException
     */
    public function __construct(NameConverterInterface $innerNameConverter)
    {
        $this->innerNameConverter = $innerNameConverter;
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * @param string      $propertyName
     * @param string|null $class
     * @param string|null $format
     * @param array       $context
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    public function normalize($propertyName, string $class = null, string $format = null, array $context = []): string
    {
        if ($class !== null) {
            $virtualPropertyName = $this->getVirtualPropertyName($propertyName, $class, $context);

            if ($virtualPropertyName !== null) {
                return $virtualPropertyName;
            }
        }

        return $this->innerNameConverter instanceof AdvancedNameConverterInterface
            ? $this->innerNameConverter->normalize($propertyName, $class, $format, $context)
            : $this->innerNameConverter->normalize($propertyName);
    }

    /**
     * @param string      $propertyName
     * @param string|null $class
     * @param string|null $format
     * @param array       $context
     *
     * @return string
     */
    public function denormalize($propertyName, string $class = null, string $format = null, array $context = []): string
    {

        return $this->innerNameConverter instanceof AdvancedNameConverterInterface
            ? $this->innerNameConverter->denormalize($propertyName, $class, $format, $context)
            : $this->innerNameConverter->denormalize($propertyName);
    }

    /**
     * @param string $propertyName
     * @param string $class
     * @param array  $context
     *
     * @return string|null
     *
     * @throws \ReflectionException
     */
    protected function getVirtualPropertyName(string $propertyName, string $class, array $context): ?string
    {
        $reflectionClass = new \ReflectionClass($class);
        $groups = $context['groups'] ?? [];
        $methodName = static::startsWith($propertyName, ['is', 'has', 'can', 'does'])
            ? $propertyName
            : 'get' . ucfirst($propertyName);

        $virtualProperty = $this->findVirtualProperty($reflectionClass, $methodName);

        if ($virtualProperty !== null && static::hasCommonEntry((array) $groups, (array) $virtualProperty->groups)) {
            return $virtualProperty->name;
        }

        return null;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param string           $methodName
     *
     * @return \ReflectionMethod|null
     *
     * @throws \ReflectionException
     */
    protected function findVirtualProperty(\ReflectionClass $reflectionClass, string $methodName) :?VirtualProperty
    {
        while ($reflectionClass instanceof \ReflectionClass) {
            if ($reflectionClass->hasMethod($methodName)) {
                $reflectionMethod = $reflectionClass->getMethod($methodName);
                $virtualProperty = $this->annotationReader->getMethodAnnotation($reflectionMethod, VirtualProperty::class);

                if ($virtualProperty instanceof VirtualProperty) {
                    /** @var Groups|null $groupsAnnotation */
                    $groupsAnnotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, Groups::class);

                    $virtualProperty->method = $reflectionMethod->getName();
                    $virtualProperty->groups = $groupsAnnotation !== null ? $groupsAnnotation->getGroups() : [];

                    return $virtualProperty;
                }
            }

            $reflectionClass = $reflectionClass->getParentClass();
        }

        return null;
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

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     *
     * @return boolean
     */
    protected static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strpos($haystack, (string) $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}
