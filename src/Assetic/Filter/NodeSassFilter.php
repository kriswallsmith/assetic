<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Filter\Sass\BaseSassFilter;

/**
 * Node-sass filter.
 *
 * @link https://github.com/sass/node-sass
 * @author Marco Polichetti <gremo1982@gmail.com>
 */
class NodeSassFilter extends BaseSassFilter
{
    private $nodeSassBin;
    private $nodeBin;
    private $nodePaths = array();

    // node-sass options
    private $outputStyle;
    private $indetType;
    private $indetWidth;
    private $linefeed;
    private $precision;

    public function __construct($nodeSassBin = '/usr/bin/node-sass', $nodeBin = null)
    {
        $this->nodeSassBin = $nodeSassBin;
        $this->nodeBin = $nodeBin;
    }

    /**
     * @param array $nodePaths
     */
    public function setNodePaths(array $nodePaths)
    {
        $this->nodePaths = $nodePaths;
    }

    /**
     * @param string $nodePath
     */
    public function addNodePath($nodePath)
    {
        $this->nodePaths[] = $nodePath;
    }

    /**
     * @param array $importPaths
     */
    public function setIncludePaths(array $importPaths)
    {
        parent::setLoadPaths($importPaths);
    }

    /**
     * @param string $path
     */
    public function addIncludePath($path)
    {
        parent::addLoadPath($path);
    }

    /**
     * @param string $outputStyle
     */
    public function setOutputStyle($outputStyle)
    {
        $this->outputStyle = $outputStyle;
    }

    /**
     * @param string $indetType
     */
    public function setIndetType($indetType)
    {
        $this->indetType = $indetType;
    }

    /**
     * @param string $indetWidth
     */
    public function setIndetWidth($indetWidth)
    {
        $this->indetWidth = $indetWidth;
    }

    /**
     * @param string $linefeed
     */
    public function setLinefeed($linefeed)
    {
        $this->linefeed = $linefeed;
    }

    /**
     * @param string $precision
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $pb = parent::createProcessBuilder(
            $this->nodeBin
                ? array($this->nodeBin, $this->nodeSassBin)
                : array($this->nodeSassBin)
        );

        if ($this->nodePaths) {
            $this->mergeEnv($pb);
            $pb->setEnv('NODE_PATH', implode(PATH_SEPARATOR, $this->nodePaths));
        }

        if ($this->outputStyle) {
            $pb->add('--output-style')->add($this->outputStyle);
        }

        if ($this->indetType) {
            $pb->add('--indent-type')->add($this->indetType);
        }

        if ($this->indetWidth) {
            $pb->add('--indent-width')->add($this->indetWidth);
        }

        if ($this->linefeed) {
            $pb->add('--linefeed')->add($this->linefeed);
        }

        if ($this->precision) {
            $pb->add('--precision')->add($this->precision);
        }

        array_unshift($this->loadPaths, $asset->getSourceDirectory());
        foreach ($this->loadPaths as $path) {
            if ($path = realpath($path)) {
                $pb->add('--include-path')->add($path);
            }
        }

        // input
        $pb->add(realpath($asset->getSourceDirectory()).DIRECTORY_SEPARATOR.basename($asset->getSourcePath()));

        $proc = $pb->getProcess();
        $code = $proc->run();

        if (0 !== $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }
}
