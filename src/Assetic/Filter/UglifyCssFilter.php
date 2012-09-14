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
 * UglifyCss filter.
 *
 * @link https://github.com/fmarcia/UglifyCSS
 * @author Franck Marcia <franck.marcia@gmail.com>
 */
class UglifyCssFilter implements FilterInterface
{
    private $uglifyCssPath;
    private $nodeJsPath;

    private $expandVars;
    private $uglyComments;
    private $cuteComments;

    /**
     * @param string $uglifyCssPath Absolute path to the uglifycss executable
     * @param string $nodeJsPath Absolute path to the folder containg node.js executable
     */
    public function __construct($uglifyCssPath, $nodeJsPath = null)
    {
        $this->uglifyCssPath = $uglifyCssPath;
        $this->nodeJsPath = $nodeJsPath;
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
     * @see Assetic\Filter\FilterInterface::filterLoad()
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * Run the asset through UglifyJs
     *
     * @see Assetic\Filter\FilterInterface::filterDump()
     */
    public function filterDump(AssetInterface $asset)
    {
        $executables = array();

        if ($this->nodeJsPath !== null) {
            $executables[] = $this->nodeJsPath;
        }

        $executables[] = $this->uglifyCssPath;

        $pb = new ProcessBuilder($executables);

        if ($this->expandVars) {
            $pb->add('--expand-vars');
        }

        if ($this->uglyComments) {
            $pb->add('--ugly-comments');
        }

        if ($this->cuteComments) {
            $pb->add('--cute-comments');
        }

        // input and output files
        $input = tempnam(sys_get_temp_dir(), 'input');

        file_put_contents($input, $asset->getContent());
        $pb->add($input);

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (127 === $code) {
            throw new \RuntimeException('Path to node executable could not be resolved.');
        }

        if (0 < $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }
}
