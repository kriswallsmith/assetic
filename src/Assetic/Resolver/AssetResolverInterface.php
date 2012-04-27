<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Resolver;

/**
 * The asset resolver resolves asset string into asset instance.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface AssetResolverInterface
{
    /**
     * Parses an input string string into an asset.
     *
     * @param string $input   An input string
     * @param array  $options An array of options
     *
     * @return AssetInterface An asset
     */
    function resolve($input, array $options = array());
}
