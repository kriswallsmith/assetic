<?php

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
    private $path;
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

    /** @inheritDoc */
    public function ensureFilter(FilterInterface $filter)
    {
        $this->filters->ensure($filter);
    }

    /** @inheritDoc */
    public function load($glue = "\n")
    {
        // loop through leaves and load each asset
        $parts = array();
        foreach (new \RecursiveIteratorIterator($this) as $asset) {
            $copy = clone $asset;
            $copy->setPath($this->path);
            $copy->ensureFilter($this->filters);
            $copy->load();

            $parts[] = $copy->getContent();
        }

        $this->content = implode($glue, $parts);
    }

    /** @inheritDoc */
    public function dump($glue = "\n")
    {
        // loop through leaves and dump each asset
        $parts = array();
        foreach (new \RecursiveIteratorIterator($this) as $asset) {
            $copy = clone $asset;
            $copy->setPath($this->path);
            $copy->ensureFilter($this->filters);

            $parts[] = $copy->dump();
        }

        return implode($glue, $parts);
    }

    /** @inheritDoc */
    public function getPath()
    {
        return $this->path;
    }

    /** @inheritDoc */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /** @inheritDoc */
    public function getContent()
    {
        return $this->content;
    }

    /** @inheritDoc */
    public function setContent($content)
    {
        $this->content = $content;
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
        if ($asset instanceof \RecursiveIterator) {
            return $asset;
        } else {
            return new \RecursiveArrayIterator(array());
        }
    }

    /** @inheritDoc */
    public function hasChildren()
    {
        return current($this->assets) instanceof \RecursiveIterator;
    }
}
