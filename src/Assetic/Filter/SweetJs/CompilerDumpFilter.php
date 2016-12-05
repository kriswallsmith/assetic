<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter\SweetJs;

use Assetic\Asset\AssetInterface;

/**
 * Mozilla's sweet.js macros compiler to pure Javascript for global macros compilation.
 *
 * @link http://sweetjs.org/
 * @author Grégory PLANCHAT <g.planchat@gmail.com>
 */
class CompilerDumpFilter
    extends BaseCompilerFilter
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $this->applyFilter($asset);
    }
}
