<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer\Batch;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractBatch
{
    protected const CACHE_LIFETIME = null;

    /** @var CacheItemPoolInterface */
    private $cache;

    /** @var array<array-key, DeferredValue> */
    private $deferredValues = [];

    #[Required]
    public function setCacheItemPool(CacheItemPoolInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /** @param array-key $key */
    public function defer($key): DeferredValue
    {
        $cachedValue = $this->getCachedValue($key);

        if ($cachedValue !== null) {
            return DeferredValue::resolved($cachedValue);
        }

        $deferredValue = $this->deferredValues[$key] ?? DeferredValue::deferred($this);
        $this->deferredValues[$key] = $deferredValue;

        return $deferredValue;
    }

    public function resolve(): void
    {
        if (empty($this->deferredValues)) {
            return;
        }

        $results = $this->query(\array_keys($this->deferredValues));

        foreach ($results as $key => $value) {
            $this->setCachedValue($key, $value);
            $deferredValue = $this->deferredValues[$key] ?? null;

            if ($deferredValue !== null) {
                $this->deferredValues[$key]->setValue($value);
                unset($this->deferredValues[$key]);
            }
        }
    }

    /**
     * @param array<array-key> $keys
     *
     * @return array<array-key, mixed>
     */
    abstract protected function query(array $keys): array;

    /**
     * @param array-key $key
     * @param mixed     $value
     */
    private function setCachedValue($key, $value): void
    {
        $cacheItem = $this->cache->getItem(\get_class($this) . (string) $key)->set($value);

        if (static::CACHE_LIFETIME !== null) {
            $cacheItem->expiresAfter(new \DateInterval(static::CACHE_LIFETIME));
        }

        $this->cache->save($cacheItem);
    }

    /**
     * @param array-key $key
     *
     * @return mixed
     */
    private function getCachedValue($key)
    {
        return $this->cache->getItem(\get_class($this) . (string) $key)->get();
    }
}
