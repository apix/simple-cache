<?php

/**
 *
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license     http://opensource.org/licenses/BSD-3-Clause  New BSD License
 *
 */

namespace Apix\SimpleCache\PsrSimpleCache;

use Apix\Cache\PsrCache\Item as CacheItem;
use Psr\SimpleCache\CacheInterface as SimpleCacheInterface;
use Psr\Cache\CacheItemPoolInterface as CacheItemPool;
use Psr\Cache\InvalidArgumentException as CacheInvalidArgumentException;

/**
 * Provides a PSR-16 (SimpleCache) wrapper to PSR-6 (Cache).
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class Pool implements SimpleCacheInterface
{

    /**
     * @var CacheItemPool
     */
    protected $cache_item_pool;

    /**
     * Constructor.
     */
    public function __construct(CacheItemPool $cache_item_pool)
    {
        $this->cache_item_pool = $cache_item_pool;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        try {
            $item = $this->cache_item_pool->getItem($key);
        } catch (CacheInvalidArgumentException $e) {
            self::_rethrow($e);
        }

        return $item->isHit() ? $item->get() : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $keys = self::_normalizedKeys($keys);

        $items = array();
        foreach ($keys as $key) {
            $items[$key] = $this->get($key, $default);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        try {
            $bool = $this->cache_item_pool->hasItem($key);
        } catch (CacheInvalidArgumentException $e) {
            self::_rethrow($e);
        }

        return $bool;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->cache_item_pool->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        $keys = self::_normalizedKeys($keys);

        try {
            $bool = $this->cache_item_pool->deleteItems($keys);
        } catch (CacheInvalidArgumentException $e) {
            self::_rethrow($e);
        }

        return $bool;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        try {
            $bool = $this->cache_item_pool->deleteItem($key);
        } catch (CacheInvalidArgumentException $e) {
            self::_rethrow($e);
        }

        return $bool;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        try {
            $item = $this->cache_item_pool->getItem($key);
            $this->setItemProperties($item, $value, $ttl);
        } catch (CacheInvalidArgumentException $e) {
            self::_rethrow($e);
        }

        return $this->cache_item_pool->save($item);
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        if (!is_array($values) && !$values instanceof \Traversable) {
            throw new InvalidArgumentException(sprintf(
                'Expected an array or a \Traversable, got `%s`.',
                gettype($values)
            ));
        }

        $keys = array_keys((array) $values);

        try {
            $items = $this->cache_item_pool->getItems($keys);
        } catch (CacheInvalidArgumentException $e) {
            self::_rethrow($e);
        }

        $success = true;
        foreach ($items as $key => $item) {
            try {
                $this->setItemProperties($item, $values[$key], $ttl);
            } catch (CacheInvalidArgumentException $e) {
                self::_rethrow($e);
            }

            $success = $success
                       && $this->cache_item_pool->saveDeferred($item);
        }

        return $success && $this->cache_item_pool->commit();
    }

    /**
     * Sets the properties of an item object.
     *
     * @param CacheItem              $item
     * @param mixed                  $value The item value (unserialized)
     * @param integer|\DateInterval|null $ttl
     *
     * @return static The invoked object.
     */
    protected function setItemProperties(
        CacheItem $item, $value, $ttl = null
    ) {
        return $item->set($value)
                    ->expiresAfter($ttl);
    }

    private static function _normalizedKeys($keys)
    {
        if (!is_array($keys)) {
            if (!$keys instanceof \Traversable) {
                throw new InvalidArgumentException(sprintf(
                    'Expected an array or a \Traversable, got `%s`.',
                    gettype($keys)
                ));
            }

            $keys = iterator_to_array($keys, false);
        }

        return $keys;
    }

    private static function _rethrow(CacheInvalidArgumentException $e)
    {
        throw new InvalidArgumentException(
            $e->getMessage(), $e->getCode(), $e
        );
    }

    /**
     * Returns the cache adapter for this pool.
     *
     * @return CacheAdapter
     */
    public function getCacheAdapter()
    {
        return $this->cache_item_pool->getCacheAdapter();
    }
}
