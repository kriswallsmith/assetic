<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Cache\CacheInterface;
use Assetic\Filter\FilterInterface;

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
            $this->asset->setContent($this->cache->get($cacheKey));
            return;
        }

        $this->asset->load($additionalFilter);
        $this->cache->set($cacheKey, $this->asset->getContent());
    }

    public function dump(FilterInterface $additionalFilter = null)
    {
        $cacheKey = self::getCacheKey($this->asset, $additionalFilter, 'dump');
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $content = $this->asset->dump($additionalFilter);
        $this->cache->set($cacheKey, $content);

        return $content;
    }

    public function getContent()
    {
        return $this->asset->getContent();
    }

    public function setContent($content)
    {
        $this->asset->setContent($content);
    }

    public function getSourceUrl()
    {
        return $this->asset->getSourceUrl();
    }

    public function getTargetUrl()
    {
        return $this->asset->getTargetUrl();
    }

    public function setTargetUrl($targetUrl)
    {
        $this->asset->setTargetUrl($targetUrl);
    }

    public function getLastModified()
    {
        return $this->asset->getLastModified();
    }

    /**
     * Returns a cache key for the current asset.
     *
     * The key is composed of everything but an asset's content:
     *
     *  * source url
     *  * last modified
     *  * filters
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

        $cacheKey  = $asset->getSourceUrl();
        $cacheKey .= $asset->getTargetUrl();
        $cacheKey .= $asset->getLastModified();

        foreach ($asset->getFilters() as $filter) {
            $cacheKey .= serialize($filter);
        }

        return md5($cacheKey.$salt);
    }
}
