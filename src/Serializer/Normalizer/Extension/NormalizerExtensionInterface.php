<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension;

/**
 * Interface NormalizerExtensionInterface.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
interface NormalizerExtensionInterface
{
    /**
     * Extends the previously normalized data
     * Return the $normalizedData when over.
     *
     * @param mixed                                         $object
     * @param array|string|int|float|bool|\ArrayObject|null $normalizedData
     *
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function extends($object, $normalizedData, ?string $format = null, array $context = []);

    /**
     * Check whether the given object is supported by the extension.
     *
     * @param mixed                                         $object
     * @param array|string|int|float|bool|\ArrayObject|null $normalizedData
     */
    public function supportsExtension($object, $normalizedData, ?string $format = null, array $context = []): bool;
}
