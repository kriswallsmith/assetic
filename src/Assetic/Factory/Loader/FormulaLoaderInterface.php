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

/**
 * Loads formulae.
 *
 * Each concrete loader is responsible for its own configuration interface.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface FormulaLoaderInterface
{
    /**
     * Checks if the current loader's formulae are fresher than a timestamp.
     *
     * @param integer $timestamp A UNIX timestamp
     *
     * @return Boolean True if the timestamp is fresh
     */
    function isFresh($timestamp);

    /**
     * Loads formulae.
     *
     * @return array An array of formulae
     */
    function load();
}
