<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Loader;

use Assetic\Factory\Resource\ResourceInterface;

/**
 * Loads formulae from resources.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface FormulaLoaderInterface
{
    /**
     * Checks if the current loader supports the supplied resource.
     *
     * @param ResourceInterface $resource A resource
     *
     * @return Boolean True if the loader knows how to load from the supplied resource
     */
    function supports(ResourceInterface $resource);

    /**
     * Loads formulae from a resource.
     *
     * @param ResourceInterface $resource A resource
     *
     * @return array An array of formulae
     */
    function load(ResourceInterface $resource);
}
