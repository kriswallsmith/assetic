<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
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
    protected function filterAllUrls($content, $callback)
    {
        $content = $this->filterUrls($content, $callback);
        $content = $this->filterImports($content, $callback);

        return $content;
    }

    protected function filterUrls($content, $callback)
    {
        return preg_replace_callback('/url\((["\']?)(?<url>.*?)(\\1)\)/', $callback, $content);
    }

    protected function filterImports($content, $callback)
    {
        return preg_replace_callback('/import (["\'])(?<url>.*?)(\\1)/', $callback, $content);
    }
}
