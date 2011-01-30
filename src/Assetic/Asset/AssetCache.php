<?php

namespace Assetic\Asset;

use Assetic\Cache\CacheInterface;
use Assetic\Filter\FilterInterface;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Caches an asset to avoid the cost of loading and dumping.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetCache implements AssetInterface
{
    private $asset;
    private $cache;

    public function __construct(AssetInterface $asset, CacheInterface $cache)
    {
        $this->asset = $asset;
        $this->cache = $cache;
    }

    public function ensureFilter(FilterInterface $filter)
    {
        $this->asset->ensureFilter($filter);
    }

    public function getFilters()
    {
        return $this->asset->getFilters();
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $cacheKey = self::getCacheKey($this->asset, $additionalFilter, 'load');
        if ($this->cache->has($cacheKey)) {
            $this->asset->setBody($this->cache->get($cacheKey));
            return;
        }

        $this->asset->load($additionalFilter);
        $this->cache->set($cacheKey, $this->asset->getBody());
    }

    public function dump(FilterInterface $additionalFilter = null)
    {
        $cacheKey = self::getCacheKey($this->asset, $additionalFilter, 'dump');
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $body = $this->asset->dump($additionalFilter);
        $this->cache->set($cacheKey, $body);

        return $body;
    }

    public function getUrl()
    {
        return $this->asset->getUrl();
    }

    public function setUrl($url)
    {
        $this->asset->setUrl($url);
    }

    public function getBody()
    {
        return $this->asset->getBody();
    }

    public function setBody($body)
    {
        $this->asset->setBody($body);
    }

    public function getContext()
    {
        return $this->asset->getContext();
    }

    public function setContext(AssetInterface $context = null)
    {
        $this->asset->setContext($context);
    }

    public function getContentType()
    {
        return $this->asset->getContentType();
    }

    public function getLastModified()
    {
        return $this->asset->getLastModified();
    }

    /**
     * Returns a cache key for the current asset.
     *
     * The key is composed of everything but an asset's body:
     *
     *  * url
     *  * filters
     *  * context (recursive)
     *
     * @param AssetInterface  $asset            The asset
     * @param FilterInterface $additionalFilter Any additional filter being applied
     * @param string          $salt             Salt for the key
     *
     * @return string A key for identifying the current asset
     */
    static private function getCacheKey(AssetInterface $asset, FilterInterface $additionalFilter = null, $salt = '')
    {
        if ($additionalFilter) {
            $asset = clone $asset;
            $asset->ensureFilter($additionalFilter);
        }

        $cacheKey = $asset->getUrl();

        foreach ($asset->getFilters() as $filter) {
            $cacheKey .= serialize($filter);
        }

        $context = $asset->getContext();
        if ($context && $asset !== $context) {
            $cacheKey .= self::getCacheKey($context);
        }

        return md5($cacheKey.$salt);
    }
}
