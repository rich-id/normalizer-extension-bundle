<?php declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Handler;

use RichCongress\Bundle\UnitBundle\TestCase\TestCase;
use RichCongress\NormalizerExtensionBundle\Serializer\Handler\CircularReferenceHandler;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Entity\DummyEntityWithId;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Entity\DummyEntityWithKeyname;
use Tests\RichCongress\NormalizerExtensionBundle\Resources\Entity\DummyEntity;

/**
 * Class CircularReferenceHandlerTest
 *
 * @package   RichCongress\NormalizerExtensionBundle\Tests\Handler
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 *
 * @covers \RichCongress\NormalizerExtensionBundle\Serializer\Handler\CircularReferenceHandler
 */
class CircularReferenceHandlerTest extends TestCase
{
    /**
     * @var CircularReferenceHandler
     */
    protected $handler;

    /**
     * @return void
     */
    protected function beforeTest(): void
    {
        $this->handler = new CircularReferenceHandler();
    }

    /**
     * @return void
     */
    public function testInvoke(): void
    {
        $entity = new DummyEntityWithId();
        $result = ($this->handler)($entity, null, []);
        self::assertSame(1, $result);

        $entity = new DummyEntityWithKeyname();
        $result = ($this->handler)($entity, null, []);
        self::assertSame('the_keyname', $result);

        $entity = new DummyEntity();
        $result = ($this->handler)($entity, null, []);
        self::assertNull($result);
    }
}
