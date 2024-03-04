<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\Model;

/**
 * Class DummyEntityWithKeyname.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class DummyModelWithKeyname
{
    public function getKeyname(): string
    {
        return 'the_keyname';
    }
}
