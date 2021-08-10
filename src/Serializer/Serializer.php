<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer;

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
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>|string|int|float|bool|\ArrayObject<string, mixed>|null
     */
    public function normalize($data, ?string $format = null, array $context = [])
    {
        $normalizedData = parent::normalize($data, $format, $context);

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
}
