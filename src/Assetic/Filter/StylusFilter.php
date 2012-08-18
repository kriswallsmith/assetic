<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Loads STYL files.
 *
 * @link http://learnboost.github.com/stylus/
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class StylusFilter implements FilterInterface
{
    private $stylusPath;
    private $nodePath;
    private $modulesPath;

    // Stylus options
    private $compress;
    private $useNib;

    /**
     * Constructs filter.
     *
     * @param string $stylusPath      The path to the stylus binary
     * @param string $nodePath        The path to the node binary
     * @param array  $nodeModulesPath An array of node paths
     */
    public function __construct($stylusPath = '/usr/bin/stylus', $nodePath = '/usr/bin/node', array $nodeModulesPath = array())
    {
        $this->stylusPath = $stylusPath;
        $this->nodePath = $nodePath;
        $this->modulesPath = $nodeModulesPath;
    }

    /**
     * Enable output compression.
     *
     * @param   boolean     $compress
     */
    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    /**
     * Enable the use of Nib
     *
     * @param   boolean     $useNib
     */
    public function setUseNib($useNib)
    {
        $this->useNib = $useNib;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $pb = new ProcessBuilder(array(
            $this->nodePath,
            $this->stylusPath,
        ));

        for ($i = count($this->modulesPath) - 1; $i >= 0; $i--) {
            $pb
                ->add('--include')
                ->add($this->modulesPath[$i])
            ;
        }

        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();
        
        if ($root && $path) {
            $pb
                ->add('--include')
                ->add(dirname($root.'/'.$path))
            ;
        }

        if ($this->compress) {
            $pb->add('--compress');
        }

        if ($this->useNib) {
            $pb
                ->add('--use')
                ->add('nib')
            ;
        }

        // We need to override stdin as it's the only way to use stdout to fetch the results
        // (otherwise, stylus overwrites the input file if we try using a temporary file without the .styl extension)
        $pb->setInput($asset->getContent());
        $proc = $pb->getProcess();
        $code = $proc->run();

        if (0 < $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
    }
}
