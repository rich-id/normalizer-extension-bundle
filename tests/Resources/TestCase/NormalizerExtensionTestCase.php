<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\TestCase;

use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\NormalizerExtensionInterface;
use RichCongress\NormalizerExtensionBundle\Serializer\Serializer;
use RichCongress\TestSuite\TestCase\TestCase;
use RichCongress\WebTestBundle\TestCase\Internal\WebTestCase;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class NormalizerExtensionTestCase.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class NormalizerExtensionTestCase extends TestCase
{
    /** @var NormalizerExtensionInterface */
    protected $normalizerExtension;

    /** @var NormalizerInterface */
    protected $normalizer;

    public function setUp(): void
    {
        $this->setUpTestCase();

        if (WebTestCase::isEnabled()) {
            /** @var NormalizerInterface $serializer */
            $serializer = $this->getService('serializer');
            $this->normalizer = $serializer;

            return;
        }

        // In this conditions, the ObjectNormalizer will serialize ALL properties whatever the serialization groups
        $this->normalizer = new Serializer([new ObjectNormalizer()], [], [$this->normalizerExtension]);

        $this->beforeTest();
    }

    /**
     * @param mixed           $object
     * @param string[]|string $groups
     *
     * @return array<string, mixed>|null
     */
    public function normalize($object, $groups = []): ?array
    {
        $output = $this->normalizer->normalize($object, null, [
            AbstractNormalizer::GROUPS => (array) $groups,
        ]);

        return \is_array($output) ? $output : null;
    }
}
