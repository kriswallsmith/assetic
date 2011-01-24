<?php

namespace Assetic;

use Assetic\Filter\FilterInterface;
use Assetic\Filter\NoopFilter;

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

    public function __construct()
    {
        $this->filters['_noop'] = new NoopFilter();
    }

    public function set($alias, FilterInterface $filter)
    {
        $this->filters[$alias] = $filter;
    }

    public function get($alias, $throwException = true)
    {
        if (isset($this->filters[$alias])) {
            return $this->filters[$alias];
        } elseif ($throwException) {
            throw new \InvalidArgumentException(sprintf('There is no "%s" filter.', $alias));
        } else {
            return $this->filters['_noop'];
        }
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
