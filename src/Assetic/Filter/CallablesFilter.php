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
 * A filter that wraps callables.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CallablesFilter extends BaseFilter implements FilterInterface
{
    private $loader;
    private $dumper;

    public function setDumper($dumper)
    {
        $this->dumper = $dumper;
    }

    public function getDumper()
    {
        return $this->dumper;
    }

    public function setLoader($loader)
    {
        $this->loader = $loader;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function filterLoad(AssetInterface $asset)
    {
        if (null !== $callable = $this->loader) {
            $callable($asset);
        }
    }

    public function filterDump(AssetInterface $asset)
    {
        if (null !== $callable = $this->dumper) {
            $callable($asset);
        }
    }
}
