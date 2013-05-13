<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * A filter manipulates an asset at load and dump.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface FilterInterface
{
    /**
     * Array of key-value pairs of options
     *
     * @param array $options Key-value options
     */
    public function __construct(array $options = array());

    /**
     * Filters an asset after it has been loaded.
     *
     * @param AssetInterface $asset An asset
     */
    public function filterLoad(AssetInterface $asset);

    /**
     * Filters an asset just before it's dumped.
     *
     * @param AssetInterface $asset An asset
     */
    public function filterDump(AssetInterface $asset);
}
