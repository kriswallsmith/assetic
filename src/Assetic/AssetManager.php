<?php

namespace Assetic;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\Filterable;
use Assetic\Filter\FilterCollection;
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
 * Manages assets.
 *
 * Filters applied to the asset manager will be applied to all assets it
 * manages.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetManager implements Filterable
{
    private $filters;
    private $assets = array();

    public function __construct()
    {
        $this->filters = new FilterCollection();
    }

    /** @inheritDoc */
    public function ensureFilter(FilterInterface $filter)
    {
        $this->filters->ensure($filter);
    }

    /**
     * Gets an asset by name.
     *
     * @param string $name The asset name
     *
     * @return AssetInterface The asset
     *
     * @throws InvalidArgumentException If there is no asset by that name
     */
    public function get($name)
    {
        if (!isset($this->assets[$name])) {
            throw new \InvalidArgumentException(sprintf('There is no "%s" asset.', $name));
        }

        $asset = $this->assets[$name];
        $asset->ensureFilter($this->filters);

        return $asset;
    }

    /**
     * Checks if the current asset manager has a certain asset.
     *
     * @param string $name an asset name
     *
     * @return Boolean True if the asset has been set, false if not
     */
    public function has($name)
    {
        return isset($this->assets[$name]);
    }

    /**
     * Registers an asset to the current asset manager.
     *
     * @param string         $name  The asset name
     * @param AssetInterface $asset The asset
     */
    public function set($name, AssetInterface $asset)
    {
        $this->assets[$name] = $asset;
    }
}
