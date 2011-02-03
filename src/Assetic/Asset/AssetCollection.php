<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

/**
 * A collection of assets.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetCollection implements AssetInterface, \RecursiveIterator
{
    private $assets = array();
    private $filters;
    private $targetUrl;
    private $content;

    /**
     * Constructor.
     *
     * @param array $assets  Assets for the current collection
     * @param array $filters Filters for the current collection
     */
    public function __construct($assets = array(), $filters = array())
    {
        foreach ($assets as $asset) {
            $this->add($asset);
        }

        $this->filters = new FilterCollection($filters);
    }

    /**
     * Adds an asset to the current collection.
     *
     * @param AssetInterface $asset An asset
     */
    public function add(AssetInterface $asset)
    {
        $this->assets[] = $asset;
    }

    public function ensureFilter(FilterInterface $filter)
    {
        $this->filters->ensure($filter);
    }

    public function getFilters()
    {
        return $this->filters->all();
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        // loop through leaves and load each asset
        $parts = array();
        foreach (new \RecursiveIteratorIterator(new AssetCollectionIterator($this)) as $asset) {
            $asset->load($additionalFilter);
            $parts[] = $asset->getContent();
        }

        $this->content = implode("\n", $parts);
    }

    public function dump(FilterInterface $additionalFilter = null)
    {
        // loop through leaves and dump each asset
        $parts = array();
        foreach (new \RecursiveIteratorIterator(new AssetCollectionIterator($this)) as $asset) {
            $parts[] = $asset->dump($additionalFilter);
        }

        return implode("\n", $parts);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * An aggregation of assets does not have a single source URL.
     */
    public function getSourceUrl()
    {
        return null;
    }

    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;
    }

    /**
     * Returns the highest last-modified value of all assets in the current collection.
     *
     * @return integer|null A UNIX timestamp
     */
    public function getLastModified()
    {
        $lastModified = null;
        foreach ($this->assets as $asset) {
            $mtime = $asset->getLastModified();
            if ($lastModified < $mtime) {
                $lastModified = $mtime;
            }
        }

        return $lastModified;
    }

    public function current()
    {
        $asset = clone current($this->assets);
        $asset->ensureFilter($this->filters);

        return $asset;
    }

    public function key()
    {
        return key($this->assets);
    }

    public function next()
    {
        return next($this->assets);
    }

    public function rewind()
    {
        return reset($this->assets);
    }

    public function valid()
    {
        return false !== current($this->assets);
    }

    public function getChildren()
    {
        return current($this->assets);
    }

    public function hasChildren()
    {
        return current($this->assets) instanceof self;
    }
}
