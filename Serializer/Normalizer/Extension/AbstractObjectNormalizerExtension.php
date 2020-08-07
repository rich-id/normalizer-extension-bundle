<?php declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension;

use RichCongress\NormalizerExtensionBundle\Exception\AttributeNotFoundException;
use RichCongress\NormalizerExtensionBundle\Exception\SkipSerializationException;

/**
 * Class AbstractObjectNormalizerExtension
 *
 * @package   RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
abstract class AbstractObjectNormalizerExtension implements NormalizerExtensionInterface
{
    /**
     * /!\ Needs to be overriden
     * Class targetted by this normalizer
     *
     * @var string
     */
    public static $objectClass;

    /**
     * Prefix to add before every context
     */
    public static $contextPrefix = '';

    /**
     * @var string
     */
    protected $format;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var string
     */
    protected $currentPropertyName;

    /**
     * @var string
     */
    protected $currentPropertyGroup;

    /**
     * Get supported serialization groups
     * Must return an array of ['context' => ['propertyName1', 'propertyName2']] or ['context' => 'propertyName']
     *
     * For each property name, a function "getPropertyName(object)" must be created
     * If the property name begins by "is" or "has", for instance "isBeautiful", the function "isBeautiful(object)" must be created instead
     *
     * @return array
     */
    abstract public static function getSupportedGroups(): array;

    /**
     * @param mixed                                         $object
     * @param array|string|int|float|bool|\ArrayObject|null $normalizedData
     * @param string|null                                   $format
     * @param array                                         $context
     *
     * @return array|string|int|float|bool|\ArrayObject|null
     *
     * @throws \ReflectionException
     */
    public function extends($object, $normalizedData, string $format = null, array $context = [])
    {
        $this->format = $format;
        $this->context = $context;
        $groups = $context['groups'] ?? [];
        $groups = (array) $groups;
        $serializationGroups = static::getSerializationGroups();

        foreach ($serializationGroups as $propertyGroup => $propertyNames) {
            $this->currentPropertyGroup = $propertyGroup;

            if (!in_array($this->currentPropertyGroup, $groups, true)) {
                continue;
            }

            foreach ($propertyNames as $propertyName) {
                $this->currentPropertyName = $propertyName;

                try {
                    $normalizedData[$propertyName] = $this->getValue($propertyName, $object);
                } catch (SkipSerializationException $e) {
                    continue;
                }
            }
        }

        return $normalizedData;
    }

    /**
     * @return array
     */
    public static function getSerializationGroups(): array
    {
        $groups = static::getSupportedGroups();

        $keys = array_map(
            static function (string $key) {
                return static::$contextPrefix . $key;
            },
            array_keys($groups)
        );

        $values = array_map(
            static function (string $value) {
                return (array) $value;
            },
            array_values($groups)
        );

        return array_combine($keys, $values);
    }

    /**
     * @param mixed                                         $object
     * @param array|string|int|float|bool|\ArrayObject|null $normalizedData
     * @param string|null                                   $format
     * @param array                                         $context
     *
     * @return bool
     */
    public function supportsExtension($object, $normalizedData, string $format = null, array $context = []): bool
    {
        return $object instanceof static::$objectClass && \is_array($normalizedData);
    }

    /**
     * @param string $propertyName
     * @param mixed  $object
     *
     * @return mixed
     *
     * @throws \ReflectionException
     * @throws SkipSerializationException
     */
    protected function getValue(string $propertyName, $object)
    {
        $callbackMethod = static::getCallbackMethod($propertyName);

        if ($callbackMethod === null) {
            $classes = [static::class, \get_class($object)];

            throw new \LogicException(
                sprintf(
                    'The method to get the property \'%s\' not found from the following classes: %s',
                    $propertyName,
                    implode(', ', $classes)
                )
            );
        }

        return $callbackMethod->getDeclaringClass()->getName() === static::class
            ? ([$this, $callbackMethod->getName()])($object)
            : ([$object, $callbackMethod->getName()])();
    }

    /**
     * @param string $propertyName
     *
     * @return \ReflectionMethod|null
     *
     * @throws \ReflectionException
     */
    public static function getCallbackMethod(string $propertyName): ?\ReflectionMethod
    {
        $normalizerReflectionClass = new \ReflectionClass(static::class);
        $callbackName = static::startsWith($propertyName, ['is', 'has', 'can', 'does'])
            ? $propertyName
            : 'get' . ucfirst($propertyName);

        if ($normalizerReflectionClass->hasMethod($callbackName)) {
            return $normalizerReflectionClass->getMethod($callbackName);
        }

        $objectReflectionClass = new \ReflectionClass(
            $normalizerReflectionClass->getStaticPropertyValue('objectClass')
        );

        return $objectReflectionClass->hasMethod($callbackName)
            ? $objectReflectionClass->getMethod($callbackName)
            : null;
    }

    /**
     * @param string $id
     *
     * @return mixed
     *
     * @throws AttributeNotFoundException
     */
    protected function getAttribute(string $id)
    {
        if (!\array_key_exists($id, $this->context)) {
            throw new AttributeNotFoundException(
                $id,
                $this->currentPropertyGroup
            );
        }

        return $this->context[$id];
    }

    /**
     * @param string $id
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getAttributeWithDefault(string $id, $default)
    {
        return $this->context[$id] ?? $default;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     *
     * @return boolean
     */
    private static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strpos($haystack, (string) $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}
