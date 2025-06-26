<?php

declare(strict_types=1);

namespace RichCongress\NormalizerExtensionBundle\Serializer\Batch;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\Service\Attribute\Required;

/** @template T */
abstract class AbstractBatch
{
    protected const CACHE_LIFETIME = null;

    /** @var CacheItemPoolInterface */
    private $cache;

    /** @var array<array-key, DeferredValue<T>> */
    private $deferredValues = [];

    #[Required]
    public function setCacheItemPool(CacheItemPoolInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @param array-key $key
     * @return DeferredValue<T>
     */
    public function defer(mixed $key): DeferredValue
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
     * @return array<array-key, T>
     */
    abstract protected function query(array $keys): array;

    /**
     * @param array-key $key
     * @param T         $value
     */
    private function setCachedValue(mixed $key, mixed $value): void
    {
        $cacheLifetime = static::CACHE_LIFETIME;

        if ($cacheLifetime === null) {
            return;
        }

        $cacheItem = $this->cache->getItem(\get_class($this) . (string) $key)->set($value);
        $cacheItem->expiresAfter(new \DateInterval($cacheLifetime));

        $this->cache->save($cacheItem);
    }

    /**
     * @param array-key $key
     *
     * @return T
     */
    private function getCachedValue(mixed $key): mixed
    {
        if (static::CACHE_LIFETIME === null) {
            return null;
        }

        return $this->cache->getItem(\get_class($this) . (string) $key)->get();
    }
}
