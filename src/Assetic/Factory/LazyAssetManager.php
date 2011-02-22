<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory;

use Assetic\AssetManager;
use Assetic\Factory\Loader\FormulaLoaderInterface;

/**
 * A lazy asset manager is a composition of a formula loader and factory.
 *
 * It lazily loads asset formulae from resources.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class LazyAssetManager extends AssetManager
{
    private $factory;
    private $loaders;
    private $formulae;
    private $loaded;

    public function __construct(AssetFactory $factory, array $loaders = array())
    {
        $this->factory = $factory;
        $this->loaders = array();
        $this->formulae = array();
        $this->loaded = false;

        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    public function addLoader(FormulaLoaderInterface $loader)
    {
        $this->loaders[] = $loader;
        $this->loaded = false;
    }

    /**
     * Loads formulae from resources.
     */
    public function load()
    {
        foreach ($this->loaders as $loader) {
            $this->formulae += $loader->load();
        }

        $this->loaded = true;
    }

    public function get($name)
    {
        if (!$this->loaded) {
            $this->load();
        }

        if (!parent::has($name) && isset($this->formulae[$name])) {
            list($inputs, $filters, $options) = $this->formulae[$name];
            $options['name'] = $name;
            parent::set($name, $this->factory->createAsset($inputs, $filters, $options));
        }

        return parent::get($name);
    }

    public function has($name)
    {
        if (!$this->loaded) {
            $this->load();
        }

        return isset($this->formulae[$name]) || parent::has($name);
    }

    public function getNames()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return array_unique(array_merge(parent::getNames(), array_keys($this->formulae)));
    }
}
