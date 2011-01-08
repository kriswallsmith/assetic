<?php

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
     * Filters an asset after its contents have been loaded.
     */
    function filterLoad(AssetInterface $asset);

    /**
     * Filters an asset just before it's dumped.
     */
    function filterDump(AssetInterface $asset);
}
