<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Runs assets through Packager.
 *
 * @link https://github.com/kamicane/packager
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class PackagerFilter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
