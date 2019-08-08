<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter\Sass;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * Loads SASS files.
 *
 * @link http://sass-lang.com/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class SassFilter extends BaseSassFilter
{
    const STYLE_NESTED     = 'nested';
    const STYLE_EXPANDED   = 'expanded';
    const STYLE_COMPACT    = 'compact';
    const STYLE_COMPRESSED = 'compressed';

    private $sassPath;
    private $rubyPath;
    private $unixNewlines;
    private $scss;
    private $style;
    private $precision;
    private $quiet;
    private $debugInfo;
    private $lineNumbers;
    private $sourceMap;
    private $cacheLocation;
    private $noCache;
    private $compass;

    public function __construct($sassPath = '/usr/bin/sass', $rubyPath = null)
    {
        $this->sassPath = $sassPath;
        $this->rubyPath = $rubyPath;
        $this->cacheLocation = FilesystemUtils::getTemporaryDirectory();
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

    public function setPrecision($precision)
    {
        $this->precision = $precision;
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

    public function setSourceMap($sourceMap)
    {
        $this->sourceMap = $sourceMap;
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
        $sassProcessArgs = array($this->sassPath);
        if (null !== $this->rubyPath) {
            $sassProcessArgs = array_merge(explode(' ', $this->rubyPath), $sassProcessArgs);
        }

        $args = [];

        if ($dir = $asset->getSourceDirectory()) {
            $args[] = '--load-path';
            $args[] = $dir;
        }

        if ($this->unixNewlines) {
            $args[] = '--unix-newlines';
        }

        if (true === $this->scss || (null === $this->scss && 'scss' == pathinfo($asset->getSourcePath(), PATHINFO_EXTENSION))) {
            $args[] = '--scss';
        }

        if ($this->style) {
            $args[] = '--style';
            $args[] = $this->style;
        }

        if ($this->precision) {
            $args[] = '--precision';
            $args[] = $this->precision;
        }

        if ($this->quiet) {
            $args[] = '--quiet';
        }

        if ($this->debugInfo) {
            $args[] = '--debug-info';
        }

        if ($this->lineNumbers) {
            $args[] = '--line-numbers';
        }

        if ($this->sourceMap) {
            $args[] = '--sourcemap';
        }

        foreach ($this->loadPaths as $loadPath) {
            $args[] = '--load-path';
            $args[] = $loadPath;
        }

        if ($this->cacheLocation) {
            $args[] = '--cache-location';
            $args[] = $this->cacheLocation;
        }

        if ($this->noCache) {
            $args[] = '--no-cache';
        }

        if ($this->compass) {
            $args[] = '--compass';
        }



        // input
        $input = FilesystemUtils::createTemporaryFile('sass');
        file_put_contents($input, $asset->getContent());
        $args[] = $input;

        $process = $this->createProcessBuilder(array_merge($sassProcessArgs, $args));

        $code = $process->run();
        unlink($input);

        if (0 !== $code) {
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        $asset->setContent($process->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
