<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer;

use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\NormalizerExtensionInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * Class Serializer.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class Serializer extends \Symfony\Component\Serializer\Serializer
{
    /** @var array|NormalizerExtensionInterface[] */
    protected $extensions = [];

    /** Serializer constructor. */
    public function __construct(array $normalizers = [], array $encoders = [], array $extensions = [])
    {
        parent::__construct($normalizers, $encoders);

        $this->extensions = $extensions;
    }

    /**
     * @param mixed $data
     * @param null  $format
     *
     * @return array|\ArrayObject|bool|float|int|mixed|string|null
     *
     * @throws ExceptionInterface
     */
    public function normalize($data, $format = null, array $context = [])
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
