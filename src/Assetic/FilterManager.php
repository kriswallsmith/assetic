<?php

namespace Assetic;

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

    public function has($alias)
    {
        return isset($this->filters[$alias]);
    }

    public function all()
    {
        return $this->filters;
    }
}
