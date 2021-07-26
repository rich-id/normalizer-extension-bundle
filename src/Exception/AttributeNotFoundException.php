<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Exception;

/**
 * Class AttributeNotFoundException.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class AttributeNotFoundException extends \Exception
{
    /** @var string */
    protected $propertyId;

    /** @var string|null */
    protected $group;

    public function __construct(string $propertyId, ?string $group = null)
    {
        $this->propertyId = $propertyId;
        $this->group = $group;

        $message = \sprintf('You need to set the "%s" entry', $this->propertyId);

        if ($this->group !== null) {
            $message .= \sprintf(' for the "%s" group', $this->group);
        }

        parent::__construct($message);
    }
}
