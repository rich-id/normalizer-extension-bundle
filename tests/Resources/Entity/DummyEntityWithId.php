<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\Entity;

/**
 * Class DummyEntityWithId.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class DummyEntityWithId
{
    public function getId(): int
    {
        return 1;
    }
}
