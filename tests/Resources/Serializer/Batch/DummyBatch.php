<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Tests\Resources\Serializer\Batch;

use RichCongress\NormalizerExtensionBundle\Serializer\Batch\AbstractBatch;

class DummyBatch extends AbstractBatch
{
    /** @var array<mixed> */
    public $calls = [];

    /** @var array-key|null */
    public $ignoredKey;

    public function query(array $keys): array
    {
        $this->calls[] = $keys;

        $result = \array_combine($keys, $keys);
        unset($result[$this->ignoredKey]);

        return $result;
    }
}
