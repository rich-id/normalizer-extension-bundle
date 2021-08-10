<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class DummyEntity.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2019 RichCongress (https://www.richcongress.com)
 *
 * @ORM\Entity
 */
class DummyEntity
{
    /**
     * @var bool
     *
     * @Groups({"dummy_entity_boolean_value"})
     */
    public $booleanValue;

    public function isEntityBoolean(): bool
    {
        return true;
    }
}
