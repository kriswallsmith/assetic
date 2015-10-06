<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Util;

/**
 * CSS Utils.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
abstract class CssUtils
{
    const REGEX_URLS            = '/url\((["\']?)(?P<url>.*?)(\\1)\)/';
    const REGEX_IMPORTS         = '/@import (?:url\()?(\'|"|)(?P<url>[^\'"\)\n\r]*)\1\)?;?/';
    const REGEX_IMPORTS_NO_URLS = '/@import (?!url\()(\'|"|)(?P<url>[^\'"\)\n\r]*)\1;?/';
    const REGEX_IE_FILTERS      = '/src=(["\']?)(?P<url>.*?)\\1/';
    const REGEX_COMMENTS        = '/(\/\*[^*]*\*+(?:[^\/][^*]*\*+)*\/)/';

    const ANALYZER = '\Assetic\Util\Analyzer\CssAnalyzer';

    /**
     * Filters all references -- url() and "@import" -- through a callable.
     *
     * @param string   $content  The CSS
     * @param callable $callback A PHP callable
     *
     * @return string The filtered CSS
     */
    public static function filterReferences($content, $callback)
    {
        $content = static::filterUrls($content, $callback);
        $content = static::filterImports($content, $callback, false);
        $content = static::filterIEFilters($content, $callback);

        return $content;
    }

    /**
     * Filters all CSS url()'s through a callable.
     *
     * @param string   $content  The CSS
     * @param callable $callback A PHP callable
     *
     * @return string The filtered CSS
     */
    public static function filterUrls($content, $callback)
    {
        $pattern = static::REGEX_URLS;

        return static::filterCommentless($content, function ($part) use (& $callback, $pattern) {
            return preg_replace_callback($pattern, $callback, $part);
        });
    }

    /**
     * Filters all CSS imports through a callable.
     *
     * @param string   $content    The CSS
     * @param callable $callback   A PHP callable
     * @param Boolean  $includeUrl Whether to include url() in the pattern
     *
     * @return string The filtered CSS
     */
    public static function filterImports($content, $callback, $includeUrl = true)
    {
        $pattern = $includeUrl ? static::REGEX_IMPORTS : static::REGEX_IMPORTS_NO_URLS;

        return static::filterCommentless($content, function ($part) use (& $callback, $pattern) {
            return preg_replace_callback($pattern, $callback, $part);
        });
    }

    /**
     * Filters all IE filters (AlphaImageLoader filter) through a callable.
     *
     * @param string   $content  The CSS
     * @param callable $callback A PHP callable
     *
     * @return string The filtered CSS
     */
    public static function filterIEFilters($content, $callback)
    {
        $pattern = static::REGEX_IE_FILTERS;

        return static::filterCommentless($content, function ($part) use (& $callback, $pattern) {
            return preg_replace_callback($pattern, $callback, $part);
        });
    }

    /**
     * Filters each non-comment part through a callable.
     *
     * @param string   $content  The CSS
     * @param callable $callback A PHP callable
     *
     * @return string The filtered CSS
     */
    public static function filterCommentless($content, $callback)
    {
        $analyzerClass = static::ANALYZER;
        $analyzer = new $analyzerClass($content);
        $result = '';
        $buffer = '';

        while ($analyzer->hasSteps()) {
            $step = $analyzer->step();

            if ($step['type'] === 'comment') {
                $result .= call_user_func($callback, $buffer).$step['part'];
                $buffer = '';
            } else {
                $buffer .= $step['part'];
            }
        }
        $result .= call_user_func($callback, $buffer);

        return $result;
    }

    /**
     * Extracts all references from the supplied CSS content.
     *
     * @param string $content The CSS content
     *
     * @return array An array of unique URLs
     */
    public static function extractImports($content)
    {
        $imports = array();
        static::filterImports($content, function ($matches) use (&$imports) {
            $imports[] = $matches['url'];
        });

        return array_unique($imports);
    }

    final private function __construct()
    {
    }
}
