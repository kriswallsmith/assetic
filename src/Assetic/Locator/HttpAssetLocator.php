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

use Assetic\Asset\HttpAsset;

/**
 * Transforms http url into an asset.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class HttpAssetLocator implements AssetLocatorInterface
{
    /**
     * Parses an input string into an asset.
     *
     * @param string $input   An input string
     * @param array  $options An array of options
     *
     * @return AssetInterface An asset
     */
    public function locate($input, array $options = array())
    {
        if (false !== strpos($input, '://') || 0 === strpos($input, '//')) {
            return $this->createHttpAsset($input, $options['vars']);
        }
    }

    protected function createHttpAsset($sourceUrl, $vars)
    {
        return new HttpAsset($sourceUrl, array(), false, $vars);
    }
}
