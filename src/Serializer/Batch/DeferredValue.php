<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer\Batch;

/** @template T */
final class DeferredValue
{
    /** @var T */
    private $value;

    /** @var AbstractBatch<T> */
    private $batch;

    /** @param T $value */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /** @return T */
    public function getValue()
    {
        if ($this->batch !== null && $this->value === null) {
            $this->batch->resolve();
        }

        return $this->value;
    }

    /** @param AbstractBatch<T> $batch */
    public static function deferred(AbstractBatch $batch): self
    {
        $deferredValue = new self();
        $deferredValue->batch = $batch;

        return $deferredValue;
    }

    /** @param T $value */
    public static function resolved($value): self
    {
        $deferredValue = new self();
        $deferredValue->value = $value;

        return $deferredValue;
    }
}
