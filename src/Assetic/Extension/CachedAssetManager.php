<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension;

use Assetic\Asset\AssetInterface;
use Assetic\AssetFactory;

class CachedAssetManager extends AssetManager
{
    private $factory;
    private $formulae = array();

    public function __construct($cacheFiles = array())
    {
        foreach ($cacheFiles as $cacheFile) {
            $this->formulae += require $cacheFile;
        }
    }

    public function setFactory(AssetFactory $factory)
    {
        $this->factory = $factory;
    }

    public function get($name)
    {
        if (isset($this->formulae[$name])) {
            $this->flush($name);
        }

        return parent::get($name);
    }

    public function has($name)
    {
        return isset($this->formulae[$name]) || parent::has($name);
    }

    public function set($name, AssetInterface $asset)
    {
        unset($this->formulae[$name]);

        parent::set($name, $asset);
    }

    public function all()
    {
        foreach (array_keys($this->formulae) as $name) {
            $this->flush($name);
        }

        return parent::all();
    }

    /**
     * Flushes an asset formula to the parent.
     *
     * @param string $name The formula name
     */
    private function flush($name)
    {
        $this->set($name, call_user_func_array(array($this->factory, 'createAsset'), $this->formulae[$name]));
        unset($this->formulae[$name]);
    }
}
