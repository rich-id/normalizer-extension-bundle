<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Handler;

use RichCongress\NormalizerExtensionBundle\Serializer\Handler\CircularReferenceHandler;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Model\DummyModel;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Model\DummyModelWithId;
use RichCongress\NormalizerExtensionBundle\Tests\Resources\Model\DummyModelWithKeyname;
use RichCongress\TestSuite\TestCase\TestCase;

/**
 * Class CircularReferenceHandlerTest.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 *
 * @covers \RichCongress\NormalizerExtensionBundle\Serializer\Handler\CircularReferenceHandler
 */
class CircularReferenceHandlerTest extends TestCase
{
    /** @var CircularReferenceHandler */
    protected $handler;

    protected function beforeTest(): void
    {
        $this->handler = new CircularReferenceHandler();
    }

    public function testInvoke(): void
    {
        $entity = new DummyModelWithId();
        $result = ($this->handler)($entity, null, []);
        self::assertSame(1, $result);

        $entity = new DummyModelWithKeyname();
        $result = ($this->handler)($entity, null, []);
        self::assertSame('the_keyname', $result);

        $entity = new DummyModel();
        $result = ($this->handler)($entity, null, []);
        self::assertNull($result);
    }
}
