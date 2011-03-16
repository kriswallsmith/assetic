<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/**
 * An asset has a mutable path and content and can be loaded and dumped.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface AssetInterface
{
    /**
     * Ensures the current asset includes the supplied filter.
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

    /**
     * Loads the asset into memory and applies load filters.
     *
     * You may provide an additional filter to apply during load.
     *
     * @param FilterInterface $additionalFilter An additional filter
     */
    function load(FilterInterface $additionalFilter = null);

    /**
     * Applies dump filters and returns the asset as a string.
     *
     * You may provide an additional filter to apply during dump.
     *
     * Dumping an asset should not change its state.
     *
     * If the current asset has not been loaded yet, it should be
     * automatically loaded at this time.
     *
     * @param string          $targetUrl        The URL where the dumped asset will be served
     * @param FilterInterface $additionalFilter An additional filter
     *
     * @return string The filtered content of the current asset
     */
    function dump(FilterInterface $additionalFilter = null);

    /**
     * Returns the loaded content of the current asset.
     *
     * @return string The content
     */
    function getContent();

    /**
     * Sets the content of the current asset.
     *
     * Filters can use this method to change the content of the asset.
     *
     * @param string $content The asset content
     */
    function setContent($content);

    /**
     * Returns the URL for the source asset.
     *
     * This is a web URL and can be either document-relative, root-relative,
     * or absolute.
     *
     * Some possible source paths:
     *
     *  * js/main.js
     *  * https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js
     *
     * @return string|null A web URL for the source asset, if there is one
     */
    function getSourceUrl();

    /**
     * Returns the target URL for the current asset.
     *
     * @return string|null A web URL where the asset will be dumped
     */
    function getTargetUrl();

    /**
     * Sets the target URL for the current asset.
     *
     * @param string $targetUrl A web URL where the asset will be dumped
     */
    function setTargetUrl($targetUrl);

    /**
     * Returns the time the current asset was last modified.
     *
     * @return integer|null A UNIX timestamp
     */
    function getLastModified();
}
