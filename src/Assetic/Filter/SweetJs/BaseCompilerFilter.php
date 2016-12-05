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

use Assetic\Filter\BaseNodeFilter;
use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;

/**
 * Base class for Mozilla's sweet.js macros compiler to pure Javascript.
 *
 * @link http://sweetjs.org/
 * @author Grégory PLANCHAT <g.planchat@gmail.com>
 */
abstract class BaseCompilerFilter extends BaseNodeFilter
{
    private $sweetBin;
    private $nodeBin;

    public function __construct($sweetBin = '/usr/bin/sjs', $nodeBin = null)
    {
        $this->sweetBin = $sweetBin;
        $this->nodeBin = $nodeBin;
    }

    protected function applyFilter(AssetInterface $asset)
    {
        $input = tempnam(sys_get_temp_dir(), 'assetic_sweetjs');
        file_put_contents($input, $asset->getContent());

        $pb = $this->createProcessBuilder($this->nodeBin
            ? array($this->nodeBin, $this->sweetBin)
            : array($this->sweetBin));

        $pb->add($input);
        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 !== $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }
}
