<?php

namespace Assetic\Filter;

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
}
