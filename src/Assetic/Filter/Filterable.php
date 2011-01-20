<?php

namespace Assetic\Filter;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A filterable object has filters.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface Filterable
{
    /**
     * Ensures the current filterable includes the supplied filter.
     *
     * @param FilterInterface $filter A filter
     */
    function ensureFilter(FilterInterface $filter);

    /**
     * Returns an array of filters currently applied.
     *
     * @return array An array of filters
     */
    function getFilters();
}
