<?php

namespace Assetic\Filter\Sass;

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;

/**
 * Loads SASS files.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class SassFilter implements FilterInterface
{
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

    public function __construct($sassPath = '/usr/bin/sass')
    {
        $this->sassPath = $sassPath;
        $this->cacheLocation = sys_get_temp_dir();
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

    public function filterLoad(AssetInterface $asset)
    {
        $options = array($this->sassPath);

        if ($this->unixNewlines) {
            $options[] = '--unix-newlines';
        }

        if ($this->scss) {
            $options[] = '--scss';
        }

        if ($this->style) {
            $options[] = '--style';
            $options[] = $this->style;
        }

        if ($this->quiet) {
            $options[] = '--quiet';
        }

        if ($this->debugInfo) {
            $options[] = '--debug-info';
        }

        if ($this->lineNumbers) {
            $options[] = '--line-numbers';
        }

        foreach ($this->loadPaths as $loadPath) {
            $options[] = '--load-path';
            $options[] = $loadPath;
        }

        if ($this->cacheLocation) {
            $options[] = '--cache-location';
            $options[] = $this->cacheLocation;
        }

        if ($this->noCache) {
            $options[] = '--no-cache';
        }

        // finally
        $options[] = $input = tempnam(sys_get_temp_dir(), 'assetic_sass');
        $options[] = $output = tempnam(sys_get_temp_dir(), 'assetic_sass');

        // todo: check for a valid return code
        shell_exec(implode(' ', array_map('escapeshellarg', $options)));

        $asset->setBody(file_get_contents($output));

        // cleanup
        unlink($input);
        unlink($output);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
