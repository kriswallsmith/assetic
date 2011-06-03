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
 * An asset has a mutable URL and content and can be loaded and dumped.
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
     * Clears all filters from the current asset.
     */
    function clearFilters();

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
     * Returns an absolute path or URL to the source asset's root directory.
     *
     * This value should be an absolute path to a directory in the filesystem,
     * an absolute URL with no path, or null.
     *
     * For example:
     *
     *  * '/path/to/web'
     *  * 'http://example.com'
     *  * null
     *
     * @return string|null The asset's root
     */
    function getSourceRoot();

    /**
     * Returns the relative path for the source asset.
     *
     * This value can be combined with the asset's source root (if both are
     * non-null) to get something compatible with file_get_contents().
     *
     * For example:
     *
     *  * 'js/main.js'
     *  * 'main.js'
     *  * null
     *
     * @return string|null The source asset path
     */
    function getSourcePath();

    /**
     * Returns the URL for the current asset.
     *
     * @return string|null A web URL where the asset will be dumped
     */
    function getTargetPath();

    /**
     * Sets the URL for the current asset.
     *
     * @param string $targetPath A web URL where the asset will be dumped
     */
    function setTargetPath($targetPath);

    /**
     * Returns the time the current asset was last modified.
     *
     * @return integer|null A UNIX timestamp
     */
    function getLastModified();
}
