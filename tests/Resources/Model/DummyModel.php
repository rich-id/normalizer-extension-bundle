<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\Model;

use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Class DummyEntity.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2019 RichCongress (https://www.richcongress.com)
 */
class DummyModel
{
    #[Groups(['dummy_entity_boolean_value'])]
    public $booleanValue;

    public function isEntityBoolean(): bool
    {
        return true;
    }
}
