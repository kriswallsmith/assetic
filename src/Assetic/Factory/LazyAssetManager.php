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
 * The lazy asset manager is a composition of a formula collection and factory.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class LazyAssetManager extends AssetManager
{
    private $formulae;
    private $factory;

    public function __construct(FormulaCollection $formulae, AssetFactory $factory)
    {
        $this->formulae = $formulae;
        $this->factory = $factory;
    }

    public function get($alias)
    {
        if (!parent::has($alias) && $this->formulae->has($alias)) {
            parent::set($alias, call_user_func_array(array($this->factory, 'createAsset'), $this->formulae->get($alias)));
        }

        return parent::get($alias);
    }

    public function has($alias)
    {
        return parent::has($alias) || $this->formulae->has($alias);
    }

    /**
     * Returns an array of asset names.
     *
     * @return array An array of asset names
     */
    public function getNames()
    {
        return array_unique(array_merge(parent::getNames(), $this->formulae->getNames()));
    }
}
