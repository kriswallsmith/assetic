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

/**
 * Asset collection filter iterator.
 *
 * The filter iterator is responsible for de-duplication of leaf assets based
 * on both strict equality and source URL.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @access private
 */
class AssetCollectionFilterIterator extends \RecursiveFilterIterator
{
    private $visited;
    private $sourceUrls;

    /**
     * Constructor.
     *
     * @param AssetCollectionIterator $iterator   The inner iterator
     * @param array                   $visited    An array of visited asset objects
     * @param array                   $sourceUrls An array of visited source URLs
     */
    public function __construct(AssetCollectionIterator $iterator, array $visited = array(), array $sourceUrls = array())
    {
        parent::__construct($iterator);

        $this->visited = $visited;
        $this->sourceUrls = $sourceUrls;
    }

    /**
     * Determines whether the current asset is a duplicate.
     *
     * De-duplication is performed based on either strict equality or by
     * matching source URLs.
     *
     * @return Boolean Returns true if we have not seen this asset yet
     */
    public function accept()
    {
        $asset = $this->getInnerIterator()->current(true);
        $duplicate = false;

        // check strict equality
        if (in_array($asset, $this->visited, true)) {
            $duplicate = true;
        } else {
            $this->visited[] = $asset;
        }

        // check source url
        if ($sourceUrl = $asset->getSourceUrl()) {
            if (in_array($sourceUrl, $this->sourceUrls)) {
                $duplicate = true;
            } else {
                $this->sourceUrls[] = $sourceUrl;
            }
        }

        return !$duplicate;
    }

    /**
     * Passes visited objects and source URLs to the child iterator.
     */
    public function getChildren()
    {
        return new self($this->getInnerIterator()->getChildren(), $this->visited, $this->sourceUrls);
    }
}

/**
 * Iterates over an asset collection.
 *
 * The iterator is responsible for cascading filters and target URL patterns
 * from parent to child assets.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @access private
 */
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
     *
     * @param Boolean $raw Returns the unmodified asset if true
     */
    public function current($raw = false)
    {
        $asset = current($this->assets);

        if ($raw) {
            return $asset;
        }

        // clone before making changes
        $clone = clone $asset;

        // generate a target url based on asset name
        $name = pathinfo($asset->getSourceUrl(), PATHINFO_FILENAME) ?: 'part';
        $name .= ($this->key() + 1);
        $clone->setTargetUrl(str_replace('*', $name, $this->output));

        foreach ($this->filters as $filter) {
            $clone->ensureFilter($filter);
        }

        return $clone;
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

    /**
     * @uses current()
     */
    public function getChildren()
    {
        return new self($this->current());
    }
}
