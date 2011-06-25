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

use Assetic\Asset\AssetInterface;

/**
 * Implemented by classes able to determine as asset's last modified timestamp.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface LastModifiedFilterInterface extends FilterInterface
{
    /**
     * Returns the time the asset was last modified.
     *
     * @param AssetInterface $asset An asset
     *
     * @return integer|null A UNIX timestamp
     */
    function getLastModified(AssetInterface $asset);
}
