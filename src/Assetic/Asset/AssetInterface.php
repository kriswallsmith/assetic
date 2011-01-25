<?php

namespace Assetic\Asset;

use Assetic\Filter\Filterable;
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
 * An asset has a mutable path and body and can be loaded and dumped.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface AssetInterface extends Filterable
{
    /**
     * Loads the asset into memory and applies load filters.
     *
     * You can optionally provide an additional filter to apply during load.
     *
     * @param FilterInterface $additionalFilter An additional filter
     */
    function load(FilterInterface $additionalFilter = null);

    /**
     * Applies dump filters and returns the asset as a string.
     *
     * You can optionally provide an additional filter to apply during dump.
     *
     * Dumping an asset should not change its state.
     *
     * If the current asset has not been loaded yet, it should be
     * automatically loaded at this time.
     *
     * @param FilterInterface $additionalFilter An additional filter
     *
     * @return string The filtered body of the current asset
     */
    function dump(FilterInterface $additionalFilter = null);

    /**
     * Returns the URL for the current asset.
     *
     * @return string $url A URL for the current asset
     */
    function getUrl();

    /**
     * Sets the URL for the current asset.
     *
     * @param string $url A URL for the current asset
     */
    function setUrl($url);

    /**
     * Returns the loaded body of the current asset.
     *
     * @return string The body
     */
    function getBody();

    /**
     * Sets the loaded body for the current asset.
     *
     * @param string $body The body
     */
    function setBody($body);

    /**
     * Returns the context of the current asset.
     *
     * @return AssetInterface|null The current context, if any
     */
    function getContext();

    /**
     * Sets that the current asset is in the context of another asset.
     *
     * The context can be set to null to indicate the current asset is not
     * in the context of any other asset.
     *
     * @param AssetInterface $context An asset
     */
    function setContext(AssetInterface $context = null);
}
