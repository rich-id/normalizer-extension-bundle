<?php declare(strict_types=1);

namespace Tests\RichCongress\NormalizerExtensionBundle\Resources\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class DummyEntity
 *
 * @package   RichCongress\WorkspaceBundle\Tests\Resources\Entity
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2019 RichCongress (https://www.richcongress.com)
 *
 * @ORM\Entity
 */
class DummyEntity
{
    /**
     * @var boolean
     *
     * @Groups({"dummy_entity_boolean_value"})
     */
    public $booleanValue;

    /**
     * @return boolean
     */
    public function isEntityBoolean(): bool
    {
        return true;
    }
}
