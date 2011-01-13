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
     * @param FilterInterface $additionalFilter An additional filter
     */
    function load(FilterInterface $additionalFilter = null);

    /**
     * Applies dump filters and returns the asset as a string.
     *
     * Dumping an asset should not change its state.
     *
     * @param FilterInterface $filter An additional filter
     *
     * @return string The asset
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
     * @param AssetInterface $context An asset
     */
    function setContext(AssetInterface $context = null);
}
