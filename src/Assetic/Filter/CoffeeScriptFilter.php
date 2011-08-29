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
use Assetic\Util\ProcessBuilder;

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

    // coffee options
    private $bare;

    public function __construct($coffeePath = '/usr/bin/coffee', $nodePath = '/usr/bin/node')
    {
        $this->coffeePath = $coffeePath;
        $this->nodePath = $nodePath;
    }

    public function setBare($bare)
    {
        $this->bare = $bare;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $input = tempnam(sys_get_temp_dir(), 'assetic_coffeescript');
        file_put_contents($input, $asset->getContent());

        $pb = new ProcessBuilder(array(
            $this->nodePath,
            $this->coffeePath,
            '-cp',
        ));

        if ($this->bare) {
            $pb->add('--bare');
        }

        $pb->add($input);
        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
