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
class AssetCollection implements AssetInterface, \IteratorAggregate
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

    public function all()
    {
        return $this->assets;
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
        foreach ($this as $asset) {
            $asset->load($additionalFilter);
            $parts[] = $asset->getContent();
        }

        $this->content = implode("\n", $parts);
    }

    public function dump(FilterInterface $additionalFilter = null)
    {
        // loop through leaves and dump each asset
        $parts = array();
        foreach ($this as $asset) {
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
        $mapper = function (AssetInterface $asset)
        {
            return $asset->getLastModified();
        };

        return max(array_map($mapper, $this->assets));
    }

    /**
     * Returns an iterator for looping recursively over unique leaves.
     */
    public function getIterator()
    {
        return new \RecursiveIteratorIterator(new AssetCollectionFilterIterator(new AssetCollectionIterator($this)));
    }
}

class AssetCollectionFilterIterator extends \RecursiveFilterIterator
{
    private $sourceUrls = array();

    public function accept()
    {
        $asset = $this->current();

        // no url == unique
        if (!$sourceUrl = $asset->getSourceUrl()) {
            return true;
        }

        // duplicate
        if (in_array($sourceUrl, $this->sourceUrls)) {
            return false;
        }

        // remember we've been here
        $this->sourceUrls[] = $sourceUrl;
        return true;
    }
}

class AssetCollectionIterator implements \RecursiveIterator
{
    private $assets;
    private $filters;
    private $output;

    public function __construct(AssetCollection $coll)
    {
        $this->assets = $coll->all();
        $this->filters = $coll->getFilters();

        $this->output = $coll->getTargetUrl();
        if (false === $pos = strpos($this->output, '.')) {
            $this->output .= '_*';
        } else {
            $this->output = substr($this->output, 0, $pos).'_*'.substr($this->output, $pos);
        }
    }

    /**
     * Returns a copy of the current asset with filters and a target URL applied.
     */
    public function current()
    {
        $asset = clone current($this->assets);

        // generate a target url
        if (!$name = pathinfo($asset->getTargetUrl(), PATHINFO_FILENAME)) {
            $name = 'part'.($this->key() + 1);
        }
        $asset->setTargetUrl(str_replace('*', $name, $this->output));

        foreach ($this->filters as $filter) {
            $asset->ensureFilter($filter);
        }

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

    public function hasChildren()
    {
        return current($this->assets) instanceof AssetCollection;
    }

    public function getChildren()
    {
        return new self($this->current());
    }
}
