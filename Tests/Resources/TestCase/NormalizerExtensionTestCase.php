<?php declare(strict_types=1);

namespace Tests\RichCongress\NormalizerExtensionBundle\Resources\TestCase;

use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\NormalizerExtensionInterface;
use RichCongress\NormalizerExtensionBundle\Serializer\Serializer;
use RichCongress\TestSuite\TestCase\TestCase;
use RichCongress\WebTestBundle\TestCase\Internal\WebTestCase;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class NormalizerExtensionTestCase
 *
 * @package   Tests\Resources\TestCase
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class NormalizerExtensionTestCase extends TestCase
{
    /**
     * @var NormalizerExtensionInterface
     */
    protected $normalizerExtension;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->setUpTestCase();

        if (WebTestCase::isEnabled()) {
            $this->serializer = $this->getService('serializer');

            return;
        }

        // In this conditions, the ObjectNormalizer will serialize ALL properties whatever the serialization groups
        $this->serializer = new Serializer([new ObjectNormalizer()], [], [$this->normalizerExtension]);

        $this->beforeTest();
    }

    /**
     * @param mixed        $object
     * @param array|string $groups
     *
     * @return array|null
     */
    public function normalize($object, $groups = []): ?array
    {
        return $this->serializer->normalize($object, null, [
            AbstractNormalizer::GROUPS     => $groups,
        ]);
    }
}
