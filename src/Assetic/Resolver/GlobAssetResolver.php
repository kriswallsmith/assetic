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

use Assetic\Asset\GlobAsset;

/**
 * Transforms glob path into asset.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class GlobAssetResolver extends FileAssetResolver
{
    /**
     * Parses an input string into an asset.
     *
     * @param string $input   An input string
     * @param array  $options An array of options
     *
     * @return AssetInterface An asset
     */
    public function resolve($input, array $options = array())
    {
        list($root, $path, $input) = $this->prepareRootPathInput($input, $options);

        if (false !== strpos($input, '*')) {
            return $this->createGlobAsset($input, $root, $options['vars']);
        }
    }

    protected function createGlobAsset($glob, $root = null, $vars)
    {
        return new GlobAsset($glob, array(), $root, $vars);
    }
}
