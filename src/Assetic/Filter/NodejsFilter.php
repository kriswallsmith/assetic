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
 * @author Anton Stöckl / Voycer AG (http://www.voycer.biz)
 */
class NodejsFilter implements FilterInterface
{
    private $nodePath;
    private $env;
    private $args;

    public function __construct($nodePath = '/usr/bin/node', $env = array(), $args = array())
    {
        $this->nodePath = $nodePath;
        $this->env = $env;
        $this->args = $args;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = new ProcessBuilder();
        
        if (!empty($this->env)) {
            foreach ($this->env as $name => $value) {
                $pb->setEnv($name, $value);
            }
        }
        
        $tempDir = realpath(sys_get_temp_dir()); // temp dir might be a symlink
        
        $pb->add($this->nodePath)
           ->add($input = tempnam($tempDir, 'assetic_nodejs'));
        
        file_put_contents($input, $asset->getContent());
        
        foreach ($this->args as $arg) {
            $pb->add($arg);
        }
        
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
