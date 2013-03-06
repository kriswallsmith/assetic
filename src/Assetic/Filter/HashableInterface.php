<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * A filter can implement a hash function
 *
 * @author Francisco Facioni <fran6co@gmail.com>
 */
interface HashableInterface
{
    /**
     * Generates a hash for the object
     *
     * @return string Object hash
     */
    public function hash(AssetInterface $asset);
}
