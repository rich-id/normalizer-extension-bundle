<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer;

use RichCongress\NormalizerExtensionBundle\Serializer\Batch\DeferredValue;
use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\NormalizerExtensionInterface;

/**
 * Class Serializer.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class Serializer extends \Symfony\Component\Serializer\Serializer
{
    /** @var NormalizerExtensionInterface[] */
    protected $extensions = [];

    /** @var bool */
    protected $isRoot = true;

    /**
     * {@inheritdoc}
     *
     * @param NormalizerExtensionInterface[] $extensions
     */
    public function __construct(array $normalizers = [], array $encoders = [], array $extensions = [])
    {
        parent::__construct($normalizers, $encoders);

        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed                $data
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>|string|int|float|bool|\ArrayObject<string, mixed>|DeferredValue|null
     */
    public function normalize($data, ?string $format = null, array $context = [])
    {
        $isRoot = $this->isRoot;
        $this->isRoot = false;

        try {
            $normalizedData = parent::normalize($data, $format, $context);
            $normalizedData = $this->extendsNormalizedData($normalizedData, $data, $format, $context);

            if ($isRoot) {
                $normalizedData = $this->resolveDeferredData($normalizedData);
            }
        } finally {
            $this->isRoot = $isRoot;
        }

        return $normalizedData;
    }

    /**
     * @param array<string, mixed>|string|int|float|bool|\ArrayObject<string, mixed>|null $normalizedData
     * @param mixed                                                                       $data
     * @param array<string, mixed>                                                        $context
     *
     * @return array<string, mixed>|string|int|float|bool|\ArrayObject<string, mixed>|DeferredValue|null
     */
    protected function extendsNormalizedData($normalizedData, $data, ?string $format, array $context)
    {
        foreach ($this->extensions as $extension) {
            if (!$extension instanceof NormalizerExtensionInterface) {
                continue;
            }

            if ($extension->supportsExtension($data, $normalizedData, $format, $context)) {
                $normalizedData = $extension->extends($data, $normalizedData, $format, $context);
            }
        }

        return $normalizedData;
    }

    /**
     * @param array<string, mixed>|string|int|float|bool|\ArrayObject<string, mixed>|DeferredValue|null $normalizedData
     *
     * @return array<string, mixed>|string|int|float|bool|\ArrayObject<string, mixed>|DeferredValue|null
     */
    protected function resolveDeferredData($normalizedData)
    {
        if ($normalizedData instanceof DeferredValue) {
            return $normalizedData->getValue();
        }

        if (!\is_array($normalizedData)) {
            return $normalizedData;
        }

        foreach ($normalizedData as $key => $value) {
            $normalizedData[$key] = $this->resolveDeferredData($value);
        }

        return $normalizedData;
    }
}
