<?php

namespace Assetic\Asset;

use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

/**
 * Represents an asset.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class Asset implements AssetInterface
{
    private $filters;
    private $content;
    private $path;
    private $originalContent;
    private $originalPath;

    /**
     * Constructor.
     *
     * @param string $content The content of the asset
     * @param array  $filters Filters for the asset
     */
    public function __construct($content, $filters = array())
    {
        $this->originalContent = $content;
        $this->filters = new FilterCollection($filters);
    }

    /** @inheritDoc */
    public function ensureFilter(FilterInterface $filter)
    {
        $this->filters->ensure($filter);
    }

    /** @inheritDoc */
    public function load()
    {
        $asset = clone $this;
        $asset->setContent($this->originalContent);
        $this->filters->filterLoad($asset);
        $this->setContent($asset->getContent());
    }

    /** @inheritDoc */
    public function dump()
    {
        $asset = clone $this;
        $this->filters->filterDump($asset);
        return $asset->getContent();
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

        if (null === $this->originalPath) {
            $this->originalPath = $path;
        }
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
