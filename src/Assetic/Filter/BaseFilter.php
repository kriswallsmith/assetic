<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\FilterInterface;

/**
 * Base filter class implementation
 */
abstract class BaseFilter implements FilterInterface
{
    /**
     * Filters an asset after it has been loaded.
     *
     * @param AssetInterface $asset An asset
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * Filters an asset just before it's dumped.
     *
     * @param AssetInterface $asset An asset
     */
    public function filterDump(AssetInterface $asset)
    {
    }
}
