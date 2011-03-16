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
class CoffeeScriptFilter implements FilterInterface
{
    private $coffeePath;
    private $nodePath;

    public function __construct($coffeePath = '/usr/bin/coffee', $nodePath = '/usr/bin/node')
    {
        $this->coffeePath = $coffeePath;
        $this->nodePath = $nodePath;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $options = array($this->nodePath, $this->coffeePath, '-sc');

        $proc = new Process(implode(' ', array_map('escapeshellarg', $options)), null, array(), $asset->getContent());
        $code = $proc->run();

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
