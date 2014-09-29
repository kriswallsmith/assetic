<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Resource\Loader;

/**
 * Loads resources from some source.
 *
 * The implementation should decide which sources are acceptable.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface ResourceLoaderInterface
{
    /**
     * Loads a list of resources from a source.
     *
     * @param mixed $source The source to load the resources from
     *
     * @return \Assetic\Factory\Resource\ResourceInterface[] The resources
     */
    public function load($source);
}
