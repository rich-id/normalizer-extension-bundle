<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer\Batch;

final class DeferredValue
{
    /** @var mixed */
    private $value;

    /** @var AbstractBatch */
    private $batch;

    /** @param mixed $value */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /** @return mixed */
    public function getValue()
    {
        if ($this->batch !== null && $this->value === null) {
            $this->batch->resolve();
        }

        return $this->value;
    }

    public static function deferred(AbstractBatch $batch): self
    {
        $deferredValue = new self();
        $deferredValue->batch = $batch;

        return $deferredValue;
    }

    /** @param mixed $value */
    public static function resolved($value): self
    {
        $deferredValue = new self();
        $deferredValue->value = $value;

        return $deferredValue;
    }
}
