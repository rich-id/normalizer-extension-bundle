<?php declare(strict_types=1);

namespace RichCongress\NormalizerBundle\Serializer\Annotation;

/**
 * Class SerializedName
 *
 * @package   RichCongress\NormalizerBundle\Serializer\Annotation
 * @author    Nicolas Guilloux <nguilloux@richcongress.com>
 * @copyright 2014 - 2020 RichCongress (https://www.richcongress.com)
 *
 * @Annotation
 */
class SerializedName
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $groups = [];

    /**
     * @var string
     */
    public $method;
}
