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
use Assetic\Factory\Resource\ResourceInterface;

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
    private $loader;
    private $resources;
    private $formulae;
    private $loaded;

    public function __construct(AssetFactory $factory, FormulaLoaderInterface $loader)
    {
        $this->factory = $factory;
        $this->loader = $loader;
        $this->resources = array();
        $this->formulae = array();
        $this->loaded = true;
    }

    /**
     * Adds a resource to the current asset manager.
     *
     * @param ResourceInterface $resource A resource
     */
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
        $this->loaded = false;
    }

    /**
     * Loads formulae from resources.
     */
    public function load()
    {
        foreach ($this->resources as $resource) {
            $this->formulae += $this->loader->load($resource);
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
