<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * A filter manipulates an asset at load and dump.
 *
 * @author Philipp A. Mohrenweiser <phiamo@googlemail.com>
 */
interface DependendFilterInterface extends FilterInterface
{
    /**
     * Tells us if the Asset has a dependency this filter wants to know about
     */
    function hasDependencies(AssetInterface $asset);

    /**
     * Get Last Modified Date of Dependencies
     *
     * @param AssetInterface $asset An asset
     */
    function getDependencyLastModified(AssetInterface $asset);

    /**
     * Get the defaultoptions for this dependencies
     * @return array(
     *    'pattern' => '/somepattern/'
     * );
     */
    function getDefaultOptions();
}
