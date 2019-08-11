<?php namespace Assetic\Contracts\Filter;

use Assetic\Contracts\Asset\AssetInterface;

/**
 * A filter manipulates an asset at load and dump.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface FilterInterface
{
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
