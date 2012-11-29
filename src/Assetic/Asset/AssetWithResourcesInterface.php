<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/**
 * An asset with dependency resources
 */
interface AssetWithResourcesInterface
{
    public function addResourcePath($resource);

    public function getResourcePaths();

    public function setResourcePaths(array $resourcePaths);
}
