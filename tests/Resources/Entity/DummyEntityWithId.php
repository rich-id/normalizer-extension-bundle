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
    /** @var int */
    public $id = 1;

    /** @var DummyEntity */
    public $dummyEntity;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDummyEntity(): ?DummyEntity
    {
        return $this->dummyEntity;
    }
}
