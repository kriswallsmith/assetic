<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

/**
 * An abstract filter for dealing with CSS.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
abstract class BaseCssFilter implements FilterInterface
{
    /**
     * Filters all references -- url() and "@import" -- through a callable.
     *
     * @param string $content  The CSS
     * @param mixed  $callback A PHP callable
     *
     * @return string The filtered CSS
     */
    protected function filterReferences($content, $callback, $limit = -1, & $count = 0)
    {
        $content = $this->filterUrls($content, $callback, $limit, $count);
        $content = $this->filterImports($content, $callback, $limit, $count, false);

        return $content;
    }

    /**
     * Filters all CSS url()'s through a callable.
     *
     * @param string  $content  The CSS
     * @param mixed   $callback A PHP callable
     * @param integer $limit    Limit the number of replacements
     * @param integer $count    Will be populated with the count
     *
     * @return string The filtered CSS
     */
    protected function filterUrls($content, $callback, $limit = -1, & $count = 0)
    {
        return preg_replace_callback('/url\((["\']?)(?<url>.*?)(\\1)\)/', $callback, $content, $limit, $count);
    }

    /**
     * Filters all CSS imports through a callable.
     *
     * @param string  $content    The CSS
     * @param mixed   $callback   A PHP callable
     * @param integer $limit      Limit the number of replacements
     * @param integer $count      Will be populated with the count
     * @param Boolean $includeUrl Whether to include url() in the pattern
     *
     * @return string The filtered CSS
     */
    protected function filterImports($content, $callback, $limit = -1, & $count = 0, $includeUrl = true)
    {
        $pattern = $includeUrl
            ? '/@import (?:url\()?(\'|"|)(?<url>[^\'"\)\n\r]*)\1\)?;?/'
            : '/@import (?!url\()(\'|"|)(?<url>[^\'"\)\n\r]*)\1;?/';

        return preg_replace_callback($pattern, $callback, $content, $limit, $count);
    }
}
