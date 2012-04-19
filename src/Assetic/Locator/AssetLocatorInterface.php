<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Locator;

/**
 * The asset locator creates asset from an input string.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface AssetLocatorInterface
{
    /**
     * Parses an input string string into an asset.
     *
     * @param string $input   An input string
     * @param array  $options An array of options
     *
     * @return AssetInterface An asset
     */
    function locate($input, array $options = array());
}
