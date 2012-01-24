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
 * Executes a js file with nodejs.
 *
 * @link https://github.com/AntonStoeckl/assetic/
 * @author Anton Stöckl
 */
class NodejsFilter implements FilterInterface
{
    private $nodePath;
    private $env;

    public function __construct($nodePath = '/usr/bin/node', $env = array())
    {
        $this->nodePath = $nodePath;
        $this->env = $env;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = new ProcessBuilder();
        
        if (!empty($this->env)) {
            foreach ($this->env as $name => $value) {
                $pb->setEnv($name, $value);
            }
        }
        
        $pb
            ->add($this->nodePath)
            ->add($asset->getSourceRoot() . DIRECTORY_SEPARATOR .  $asset->getSourcePath())
        ;
        

        $proc = $pb->getProcess();
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
