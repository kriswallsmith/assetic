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

/**
 * An asset manager that also knows how to create assets.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class LazyAssetManager extends AssetManager
{
    private $factory;
    private $formulae = array();

    public function __construct(AssetFactory $factory)
    {
        $this->factory = $factory;
        $factory->setAssetManager($this);
    }

    public function addFormulae(array $formulae)
    {
        array_map(array($this, 'checkName'), array_keys($formulae));

        $this->formulae = $formulae + $this->formulae;
    }

    public function setFormula($name, array $formula)
    {
        $this->checkName($name);

        $this->formulae[$name] = $formula;
    }

    public function getFormulae()
    {
        return $this->formulae;
    }

    public function get($name)
    {
        if (!parent::has($name) && isset($this->formulae[$name])) {
            $this->flush($name);
        }

        return parent::get($name);
    }

    public function has($name)
    {
        return isset($this->formulae[$name]) || parent::has($name);
    }

    public function all()
    {
        foreach (array_keys($this->formulae) as $name) {
            if (!parent::has($name)) {
                $this->flush($name);
            }
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
        static $defaults = array(array(), array(), array());

        $formula = $this->formulae[$name] + $defaults;
        $formula[2]['name'] = $name;

        $this->set($name, call_user_func_array(array($this->factory, 'createAsset'), $formula));
    }
}
