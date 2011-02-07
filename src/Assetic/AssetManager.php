<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic;

use Assetic\Asset\AssetInterface;

/**
 * Manages assets.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetManager
{
    private $assets = array();

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

        return $this->assets[$name];
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
        $this->checkName($name);

        $this->assets[$name] = $asset;
    }

    /**
     * Returns all assets.
     *
     * @return array An array of assets
     */
    public function all()
    {
        return $this->assets;
    }

    /**
     * Checks that a name is valid.
     *
     * @param string $name An asset name candidate
     *
     * @throws InvalidArgumentException If the asset name is invalid
     */
    protected function checkName($name)
    {
        if (!ctype_alnum(str_replace('_', '', $name))) {
            throw new \InvalidArgumentException(sprintf('The name "%s" is invalid.', $name));
        }
    }
}
