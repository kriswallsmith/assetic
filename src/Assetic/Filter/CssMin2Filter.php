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
 * Filters assets through CSSmin.
 *
 * @link https://raw.github.com/mrclay/minify/master/min/lib/CSSmin.php
 * @author Erik Landvall <erik@landvall.se>
 */
class CssMin2Filter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset){}

    public function filterDump(AssetInterface $asset)
    {
        $filter = new \CSSmin;
        $content = $filter->run($asset->getContent());
        $asset->setContent($content);
    }
}
