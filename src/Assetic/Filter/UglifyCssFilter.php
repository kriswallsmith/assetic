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
use Assetic\Util\FilesystemUtils;
use Symfony\Component\Process\Process;

/**
 * UglifyCss filter.
 *
 * @link https://github.com/fmarcia/UglifyCSS
 * @author Franck Marcia <franck.marcia@gmail.com>
 */
class UglifyCssFilter extends BaseNodeFilter
{
    private $uglifycssBin;
    private $nodeBin;

    private $expandVars;
    private $uglyComments;
    private $cuteComments;

    /**
     * @param string $uglifycssBin Absolute path to the uglifycss executable
     * @param string $nodeBin      Absolute path to the folder containg node.js executable
     */
    public function __construct($uglifycssBin = '/usr/bin/uglifycss', $nodeBin = null)
    {
        $this->uglifycssBin = $uglifycssBin;
        $this->nodeBin = $nodeBin;
    }

    /**
     * Expand variables
     * @param bool $expandVars True to enable
     */
    public function setExpandVars($expandVars)
    {
        $this->expandVars = $expandVars;
    }

    /**
     * Remove newlines within preserved comments
     * @param bool $uglyComments True to enable
     */
    public function setUglyComments($uglyComments)
    {
        $this->uglyComments = $uglyComments;
    }

    /**
     * Preserve newlines within and around preserved comments
     * @param bool $cuteComments True to enable
     */
    public function setCuteComments($cuteComments)
    {
        $this->cuteComments = $cuteComments;
    }

    /**
     * @see \Assetic\Filter\FilterInterface::filterLoad()
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * Run the asset through UglifyJs
     *
     * @see \Assetic\Filter\FilterInterface::filterDump()
     */
    public function filterDump(AssetInterface $asset)
    {
        $commandline =$this->nodeBin
            ? array($this->nodeBin, $this->uglifycssBin)
            : array($this->uglifycssBin);

        if ($this->expandVars) {
            array_push($commandline, '--expand-vars');
        }

        if ($this->uglyComments) {
            array_push($commandline, '--ugly-comments');
        }

        if ($this->cuteComments) {
            array_push($commandline, '--cute-comments');
        }

        // input and output files
        $input = FilesystemUtils::createTemporaryFile('uglifycss');

        file_put_contents($input, $asset->getContent());
        array_push($commandline, $input);

        $proc = new Process($commandline);
        $code = $proc->run();
        unlink($input);

        if (127 === $code) {
            throw new \RuntimeException('Path to node executable could not be resolved.');
        }

        if (0 !== $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }
}
