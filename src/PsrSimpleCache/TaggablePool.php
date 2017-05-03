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

use Apix\Cache\Adapter as CacheAdapter;
use Psr\Cache\CacheItemInterface as ItemInterface;
use Psr\Cache\CacheItemPoolInterface as CacheItemPool;
use Psr\Cache\InvalidArgumentException as CacheInvalidArgumentException;

use Apix\Cache\PsrCache\Item as CacheItem;

class TaggablePool extends Pool
{

    /**
     * The tags associated with this pool.
     * @var array|null
     */
    protected $tags = null;

    /**
     * @var CacheAdapter
     */
    protected $cache_adapter;

    /**
     * Constructor.
     */
    public function __construct(CacheItemPool $cache_item_pool)
    {
        parent::__construct($cache_item_pool);

        $this->cache_adapter = $this->cache_item_pool->getCacheAdapter();

        $options = array(
            'tag_enable' => true // wether to enable tagging
        );

        $this->cache_adapter->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function setItemProperties(
        CacheItem $item, $value, $ttl = null
    ) {
        return $item->set($value)
                    ->expiresAfter($ttl)
                    ->setTags($this->tags);
    }

    /**
     * Retrieves the cache keys for the given tag.
     *
     * @param  string $tag The cache tag to retrieve.
     * @return array  Returns an array of cache keys.
     */
    public function getMultipleByTag($tag)
    {
        $keys = $this->cache_adapter->loadTag($tag);
        $items = array();
        if ($keys) {
            foreach ($keys as $key) {
                $k = $this->cache_adapter->removePrefixKey($key);
                $items[$k] = $this->get($k);
            }
        }

        return $items;
    }

    /**
     * Removes all the cached entries associated with the given tag names.
     *
     * @param  array $tags An array of tag names (string).
     * @return bool
     */
    public function clearByTags(array $tags)
    {
        return $this->cache_adapter->clean($tags);
    }

    /**
     * Sets this pool tags.
     *
     * @param  array|null   $tags
     * @return TaggableItem The invoked object.
     */
    public function setTags(array $tags=null)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Returns this pool tags.
     *
     * @return array|null
     */
    public function getTags()
    {
        return $this->tags;
    }
}
