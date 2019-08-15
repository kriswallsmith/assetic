<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\FilterInterface;

/**
 * A collection of filters.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FilterCollection implements FilterInterface, \IteratorAggregate, \Countable
{
    private $filters = [];

    public function __construct($filters = [])
    {
        foreach ($filters as $filter) {
            $this->ensure($filter);
        }
    }

    /**
     * Checks that the current collection contains the supplied filter.
     *
     * If the supplied filter is another filter collection, each of its
     * filters will be checked.
     */
    public function ensure(FilterInterface $filter)
    {
        if ($filter instanceof \Traversable) {
            foreach ($filter as $f) {
                $this->ensure($f);
            }
        } elseif (!in_array($filter, $this->filters, true)) {
            $this->filters[] = $filter;
        }
    }

    public function all()
    {
        return $this->filters;
    }

    public function clear()
    {
        $this->filters = [];
    }

    public function filterLoad(AssetInterface $asset)
    {
        foreach ($this->filters as $filter) {
            $filter->filterLoad($asset);
        }
    }

    public function filterDump(AssetInterface $asset)
    {
        foreach ($this->filters as $filter) {
            $filter->filterDump($asset);
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->filters);
    }

    public function count()
    {
        return count($this->filters);
    }
}
