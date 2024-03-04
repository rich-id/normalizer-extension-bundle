<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\TestCase;

use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\NormalizerExtensionInterface;
use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichCongress\WebTestBundle\TestCase\Internal\WebTestCase;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class NormalizerExtensionTestCase.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
#[TestConfig('container')]
class NormalizerExtensionTestCase extends TestCase
{
    /** @var NormalizerExtensionInterface */
    protected $normalizerExtension;

    /** @var NormalizerInterface */
    protected $normalizer;

    public function setUp(): void
    {
        $this->setUpTestCase();
        $this->initNormalizer();
        $this->beforeTest();
    }

    private function initNormalizer(): void
    {
        if (!WebTestCase::isEnabled()) {
            throw new \LogicException('Missing container');
        }

        /** @var NormalizerInterface $serializer */
        $serializer = $this->getService('serializer');
        $this->normalizer = $serializer;
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
