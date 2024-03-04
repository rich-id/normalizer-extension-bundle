<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\Model;

/**
 * Class DummyEntityWithId.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class DummyModelWithId
{
    /** @var int */
    public $id = 1;

    /** @var DummyModel */
    public $dummyEntity;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDummyEntity(): ?DummyModel
    {
        return $this->dummyEntity;
    }
}
