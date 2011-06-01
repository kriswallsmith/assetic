<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter\Sass;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Util\ProcessBuilder;

/**
 * Loads SASS files.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class SassFilter implements FilterInterface
{
    const STYLE_NESTED     = 'nested';
    const STYLE_EXPANDED   = 'expanded';
    const STYLE_COMPACT    = 'compact';
    const STYLE_COMPRESSED = 'compressed';

    private $sassPath;
    private $unixNewlines;
    private $scss;
    private $style;
    private $quiet;
    private $debugInfo;
    private $lineNumbers;
    private $loadPaths = array();
    private $cacheLocation;
    private $noCache;
    private $compass;

    public function __construct($sassPath = '/usr/bin/sass')
    {
        $this->sassPath = $sassPath;
        $this->cacheLocation = realpath(sys_get_temp_dir());
    }

    public function setUnixNewlines($unixNewlines)
    {
        $this->unixNewlines = $unixNewlines;
    }

    public function setScss($scss)
    {
        $this->scss = $scss;
    }

    public function setStyle($style)
    {
        $this->style = $style;
    }

    public function setQuiet($quiet)
    {
        $this->quiet = $quiet;
    }

    public function setDebugInfo($debugInfo)
    {
        $this->debugInfo = $debugInfo;
    }

    public function setLineNumbers($lineNumbers)
    {
        $this->lineNumbers = $lineNumbers;
    }

    public function addLoadPath($loadPath)
    {
        $this->loadPaths[] = $loadPath;
    }

    public function setCacheLocation($cacheLocation)
    {
        $this->cacheLocation = $cacheLocation;
    }

    public function setNoCache($noCache)
    {
        $this->noCache = $noCache;
    }

    public function setCompass($compass)
    {
        $this->compass = $compass;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = new ProcessBuilder();
        $pb->add($this->sassPath);

        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        if ($root && $path) {
            $pb->add('--load-path')->add(dirname($root.'/'.$path));
        }

        if ($this->unixNewlines) {
            $pb->add('--unix-newlines');
        }

        if (true === $this->scss || (null === $this->scss && 'scss' == pathinfo($path, PATHINFO_EXTENSION))) {
            $pb->add('--scss');
        }

        if ($this->style) {
            $pb->add('--style')->add($this->style);
        }

        if ($this->quiet) {
            $pb->add('--quiet');
        }

        if ($this->debugInfo) {
            $pb->add('--debug-info');
        }

        if ($this->lineNumbers) {
            $pb->add('--line-numbers');
        }

        foreach ($this->loadPaths as $loadPath) {
            $pb->add('--load-path')->add($loadPath);
        }

        if ($this->cacheLocation) {
            $pb->add('--cache-location')->add($this->cacheLocation);
        }

        if ($this->noCache) {
            $pb->add('--no-cache');
        }

        if ($this->compass) {
            $pb->add('--compass');
        }

        // input
        $pb->add($input = tempnam(sys_get_temp_dir(), 'assetic_sass'));
        file_put_contents($input, $asset->getContent());

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
