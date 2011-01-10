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
    private $body;
    private $path;
    private $originalBody;

    /**
     * Constructor.
     *
     * @param string $body The body of the asset
     * @param array  $filters Filters for the asset
     */
    public function __construct($body, $filters = array())
    {
        $this->originalBody = $body;
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
        $asset->setBody($this->originalBody);
        $this->filters->filterLoad($asset);
        $this->setBody($asset->getBody());
    }

    /** @inheritDoc */
    public function dump()
    {
        $asset = clone $this;
        $this->filters->filterDump($asset);
        return $asset->getBody();
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
    public function getBody()
    {
        return $this->body;
    }

    /** @inheritDoc */
    public function setBody($body)
    {
        $this->body = $body;
    }
}
