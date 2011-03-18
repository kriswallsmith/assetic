<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Loader;

use Assetic\Factory\Resource\ResourceInterface;

/**
 * Loads formulae.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface FormulaLoaderInterface
{
    /**
     * Loads formulae from a resource.
     *
     * @param ResourceInterface $resource A resource
     *
     * @return array An array of formulae
     */
    function load(ResourceInterface $resource);
}
