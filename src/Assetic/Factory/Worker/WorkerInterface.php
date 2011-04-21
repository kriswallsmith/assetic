<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Worker;

use Assetic\Asset\AssetInterface;

/**
 * Assets are passed through factory workers before leaving the factory.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface WorkerInterface
{
    /**
     * Processes an asset.
     *
     * @param AssetInterface $asset An asset
     *
     * @return AssetInterface The processed asset
     */
    function process(AssetInterface $asset);
}
