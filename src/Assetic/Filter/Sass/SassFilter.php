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
use Assetic\Filter\Process;

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
    private $compassPath;
    private $unixNewlines;
    private $scss;
    private $compass;
    private $style;
    private $quiet;
    private $debugInfo;
    private $lineNumbers;
    private $loadPaths = array();
    private $cacheLocation;
    private $noCache;

    public function __construct($sassPath = '/usr/bin/sass', $compassPath = null)
    {
        $this->sassPath = $sassPath;
        $this->compassPath = $compassPath;
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

    public function setCompass($compass)
    {
        $this->compass = $compass;
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

        if ($this->compass) {
            //$options[] = '--compass'; // for sass > 3

            // basically, we just need to run sass with options '-r compass `compass imports`'
            // but because we use escapeshellarg, we have to preprocess the 'compass imports',
            // and explode as options, because it create options for sass
            $options[] = '-r';
            $options[] = 'compass';

            // we generate the appropriate -I paths to allow sass to parse files using compass
            // see "compass help imports"
            $proc = new Process($this->compassPath . ' imports');
            $code = $proc->run();

            if (0 < $code) {
                throw new \RuntimeException($proc->getErrorOutput());
            }

            // here is the trick because the ouput given by 'compass imports' is like "-I /path -I /path2"
            $options = array_merge($options, explode(' ', $proc->getOutput()));
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
        file_put_contents($input, $asset->getContent());

        $options[] = $output = tempnam(sys_get_temp_dir(), 'assetic_sass');

        $proc = new Process(implode(' ', array_map('escapeshellarg', $options)));
        $code = $proc->run();

        if (0 < $code) {
            unlink($input);
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent(file_get_contents($output));

        unlink($input);
        unlink($output);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
