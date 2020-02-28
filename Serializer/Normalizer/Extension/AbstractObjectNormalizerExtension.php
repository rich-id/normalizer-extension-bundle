<?php declare(strict_types=1);

namespace RichCongress\NormalizerBundle\Serializer\Normalizer\Extension;

use RichCongress\NormalizerBundle\Exception\AttributeNotFoundException;

/**
 * Class AbstractObjectNormalizerExtension
 *
 * @package   RichCongress\NormalizerBundle\Serializer\Normalizer\Extension
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
        $groups = ((array) $context['groups']) ?? [];
        $supportedGroups = static::getSupportedGroups();

        foreach ($supportedGroups as $propertyGroup => $propertyNames) {
            $this->currentPropertyGroup = static::$contextPrefix . $propertyGroup;
            $propertyNames = (array) $propertyNames;

            if (!in_array($this->currentPropertyGroup, $groups, true)) {
                continue;
            }

            foreach ($propertyNames as $propertyName) {
                $this->currentPropertyName = $propertyName;

                $callbackName = static::startsWith($propertyName, ['is', 'has', 'can', 'does'])
                    ? $propertyName
                    : 'get' . ucfirst($propertyName);

                $normalizedData[$propertyName] = $this->getValue($callbackName, $object);
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
     */
    public function supportsExtension($object, $normalizedData, string $format = null, array $context = []): bool
    {
        return $object instanceof static::$objectClass && \is_array($normalizedData);
    }

    /**
     * @param string $callbackName
     * @param mixed  $object
     *
     * @return callable
     *
     * @throws \ReflectionException
     */
    protected function getValue(string $callbackName, $object)
    {
        $normalizerReflectionClass = new \ReflectionClass($this);

        if ($normalizerReflectionClass->hasMethod($callbackName)) {
            $callback = [$this, $callbackName];

            return $callback($object);
        }

        $objectReflectionClass = new \ReflectionClass($object);

        if ($objectReflectionClass->hasMethod($callbackName)) {
            $callback = [$object, $callbackName];

            return $callback();
        }

        $classes = [
            static::class,
            \get_class($object)
        ];

        throw new \LogicException(
            sprintf(
                'The method \'%s\' not found from the following classes: %s',
                $callbackName,
                implode(', ', $classes)
            )
        );
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
