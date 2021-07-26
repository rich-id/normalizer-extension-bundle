<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer\Handler;

use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class CircularReferenceHandler.
 *
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 */
class CircularReferenceHandler
{
    /**
     * @param mixed                $object
     * @param array<string, mixed> $context
     *
     * @return mixed
     */
    public function __invoke($object, ?string $format, array $context)
    {
        $propertyAccessor = new PropertyAccessor();

        if ($propertyAccessor->isReadable($object, 'id')) {
            return $propertyAccessor->getValue($object, 'id');
        }

        if ($propertyAccessor->isReadable($object, 'keyname')) {
            return $propertyAccessor->getValue($object, 'keyname');
        }

        return null;
    }
}
