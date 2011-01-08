<?php

namespace Assetic\Asset;

use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

/**
 * Represents an asset loaded from a file.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileAsset implements AssetInterface
{
    private $path;
    private $filters;
    private $content;

    /**
     * Constructor.
     *
     * @param string $path    The absolute file system path
     * @param array  $filters Filters for the asset
     */
    public function __construct($path, $filters = array())
    {
        $this->path = $path;
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
        $asset->setContent(file_get_contents($this->path));
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
