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
 * Compiles CoffeeScript into Javascript.
 *
 * @link http://jashkenas.github.com/coffee-script/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CoffeeScriptFilter extends AbstractProcessFilter
{
    private $coffeePath;
    private $nodePath;

    public function __construct($coffeePath = '/usr/bin/coffee', $nodePath = '/usr/bin/node')
    {
        $this->coffeePath = $coffeePath;
        $this->nodePath = $nodePath;
    }

    /**
     * @param \Assetic\Asset\AssetInterface $asset
     * @return void
     * @throw \RuntimeException
     */
    public function filterLoad(AssetInterface $asset)
    {
        $options = array($this->nodePath, $this->coffeePath, '-sc');

        $process = $this->createProcess($options);
        $code = $process->run();
        if (0 < $code) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        $asset->setContent($process->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
