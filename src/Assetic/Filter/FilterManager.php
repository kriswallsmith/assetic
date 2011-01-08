<?php

namespace Assetic\Filter;

/**
 * Manages the available filters.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FilterManager
{
    private $filters = array();

    public function set($alias, FilterInterface $filter)
    {
        $this->filters[$alias] = $filter;
    }

    public function get($alias)
    {
        if (!isset($this->filters[$alias])) {
            throw new \InvalidArgumentException(sprintf('There is no "%s" filter.', $alias));
        }

        return $this->filters[$alias];
    }
}
