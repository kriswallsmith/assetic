<?php

namespace Assetic\Asset;

use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

/**
 * A package of assets.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class Package implements AssetInterface
{
    private $assets = array();
    private $filters;
    private $content;

    /**
     * Constructor.
     *
     * @param array $assets  Assets for the current package
     * @param array $filters Filters for the current package
     */
    public function __construct($assets = array(), $filters = array())
    {
        foreach ($assets as $asset) {
            $this->add($asset);
        }

        $this->filters = new FilterCollection($filters);
    }

    /**
     * Adds an asset to the current package.
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
        $parts = array();
        foreach ($this->assets as $asset) {
            $asset->load();
            $this->filters->filterLoad($asset);
            $parts[] = $asset->getContent();
        }

        $this->content = implode($glue, $parts);
    }

    /** @inheritDoc */
    public function dump($glue = "\n")
    {
        $parts = array();
        foreach ($this->assets as $asset) {
            $parts[] = $asset->dump();
        }

        // do not change the state of the current object
        $asset = clone $this;
        $asset->setContent(implode($glue, $parts));
        $this->filters->filterDump($asset);

        return $asset->getContent();
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
}
