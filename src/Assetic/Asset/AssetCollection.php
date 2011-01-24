<?php

namespace Assetic\Asset;

use Assetic\Filter\FilterCollection;
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
 * A collection of assets.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetCollection implements AssetInterface, \RecursiveIterator
{
    private $assets = array();
    private $filters;
    private $url;
    private $body;
    private $context;

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

    /** @inheritDoc */
    public function ensureFilter(FilterInterface $filter)
    {
        $this->filters->ensure($filter);
    }

    /** @inheritDoc */
    public function getFilters()
    {
        return $this->filters->all();
    }

    /** @inheritDoc */
    public function load(FilterInterface $additionalFilter = null)
    {
        $filter = clone $this->filters;
        if ($additionalFilter) {
            $filter->ensure($additionalFilter);
        }

        // loop through leaves and load each asset
        $parts = array();
        foreach (new AssetCollectionIterator($this) as $asset) {
            // snapshot
            $context = $asset->getContext();
            $asset->setContext($this->context ?: $this);

            $asset->load($filter);
            $parts[] = $asset->getBody();

            // restore
            $asset->setContext($context);
        }

        $this->body = implode("\n", $parts);
    }

    /** @inheritDoc */
    public function dump(FilterInterface $additionalFilter = null)
    {
        $filter = clone $this->filters;
        if ($additionalFilter) {
            $filter->ensure($additionalFilter);
        }

        // loop through leaves and dump each asset
        $parts = array();
        foreach (new AssetCollectionIterator($this) as $asset) {
            // snapshot
            $context = $asset->getContext();
            $asset->setContext($this->context ?: $this);

            $parts[] = $asset->dump($filter);

            // restore
            $asset->setContext($context);
        }

        return implode("\n", $parts);
    }

    /** @inheritDoc */
    public function getUrl()
    {
        return $this->url;
    }

    /** @inheritDoc */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /** @inheritDoc */
    public function getBody()
    {
        return $this->body;
    }

    /** @inheritDoc */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /** @inheritDoc */
    public function getContext()
    {
        return $this->context;
    }

    /** @inheritDoc */
    public function setContext(AssetInterface $context = null)
    {
        $this->context = $context;
    }

    /** @inheritDoc */
    public function current()
    {
        return current($this->assets);
    }

    /** @inheritDoc */
    public function key()
    {
        return key($this->assets);
    }

    /** @inheritDoc */
    public function next()
    {
        return next($this->assets);
    }

    /** @inheritDoc */
    public function rewind()
    {
        return reset($this->assets);
    }

    /** @inheritDoc */
    public function valid()
    {
        return false !== current($this->assets);
    }

    /** @inheritDoc */
    public function getChildren()
    {
        $asset = current($this->assets);
        if ($asset instanceof self) {
            return $asset;
        } else {
            return new self();
        }
    }

    /** @inheritDoc */
    public function hasChildren()
    {
        return current($this->assets) instanceof self;
    }
}
