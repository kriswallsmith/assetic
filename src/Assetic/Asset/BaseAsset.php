<?php

namespace Assetic\Asset;

use Assetic\Filter\FilterCollection;
use Assetic\Filter\FilterInterface;

/**
 * A base abstract asset.
 *
 * The methods load() and getLastModified() are left undefined, although a
 * reusable doLoad() method is available to child classes.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
abstract class BaseAsset implements AssetInterface
{
    private $filters;
    private $url;
    private $body;
    private $context;
    private $loaded;

    /**
     * Constructor.
     *
     * @param string $url     The asset URL
     * @param array  $filters Filters for the asset
     */
    public function __construct($filters = array())
    {
        $this->filters = new FilterCollection($filters);
    }

    public function ensureFilter(FilterInterface $filter)
    {
        $this->filters->ensure($filter);
    }

    public function getFilters()
    {
        return $this->filters->all();
    }

    /**
     * Encapsulates asset loading logic.
     *
     * @param string          $body             The asset body
     * @param FilterInterface $additionalFilter An additional filter
     */
    protected function doLoad($body, FilterInterface $additionalFilter = null)
    {
        $filter = clone $this->filters;
        if ($additionalFilter) {
            $filter->ensure($additionalFilter);
        }

        $asset = clone $this;
        $asset->setBody($body);

        $filter->filterLoad($asset);

        $this->setBody($asset->getBody());
        $this->loaded = true;
    }

    public function dump(FilterInterface $additionalFilter = null)
    {
        if (!$this->loaded) {
            $this->load();
        }

        $filter = clone $this->filters;
        if ($additionalFilter) {
            $filter->ensure($additionalFilter);
        }

        $asset = clone $this;
        $filter->filterDump($asset);

        return $asset->getBody();
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext(AssetInterface $context = null)
    {
        $this->context = $context;
    }
}
