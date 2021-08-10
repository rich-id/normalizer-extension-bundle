<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\Entity;

/**
 * Class DummyEntityWithKeyname.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class DummyEntityWithKeyname
{
    public function getKeyname(): string
    {
        return 'the_keyname';
    }
}
