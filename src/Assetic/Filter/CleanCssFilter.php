<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;

/**
 * CleanCss filter.
 *
 * @link https://github.com/GoalSmashers/clean-css
 * @author Jacek Jędrzejewski <http://jacek.jedrzejewski.name>
 */
class CleanCssFilter extends BaseNodeFilter
{
    private $cleancssBin;
    private $nodeBin;

    private $removeEmpty;
    private $keepLineBreaks;
    private $keepSpecialComments;

    /**
     * @param string $cleancssBin   Absolute path to the cleancss executable
     * @param string $nodeBin       Absolute path to the folder containg node.js executable
     */
    public function __construct($cleancssBin = '/usr/bin/cleancss', $nodeBin = null)
    {
        $this->cleancssBin = $cleancssBin;
        $this->nodeBin = $nodeBin;
    }

    /**
     * Remove empty elements
     * @param bool $removeEmpty True to enable
     */
    public function setRemoveEmpty($removeEmpty)
    {
        $this->removeEmpty = $removeEmpty;
    }

    /**
     * Keep line breaks
     * @param bool $keepLineBreaks True to enable
     */
    public function setKeepLineBreaks($keepLineBreaks)
    {
        $this->keepLineBreaks = $keepLineBreaks;
    }

    /**
     * Keep special comments (i.e. /*! special comment *\/)
     * @param bool $keepSpecialComments * for keeping all (default), 1 for keeping first one, 0 for removing all
     */
    public function setKeepSpecialComments($keepSpecialComments)
    {
        $this->keepSpecialComments = $keepSpecialComments;
    }

    /**
     * @see Assetic\Filter\FilterInterface::filterLoad()
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * Run the asset through CleanCSS
     *
     * @see Assetic\Filter\FilterInterface::filterDump()
     */
    public function filterDump(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder($this->nodeBin
                ? array($this->nodeBin, $this->cleancssBin)
                : array($this->cleancssBin));

        if ($this->removeEmpty) {
            $pb->add('--remove-empty');
        }

        if ($this->keepLineBreaks) {
            $pb->add('--keep-line-breaks');
        }

        if (0 === $this->keepSpecialComments) {
            $pb->add('--s0');
        } elseif (1 === $this->keepSpecialComments) {
            $pb->add('--s1');
        }

        // Skip inlining imports
        $pb->add('-s');

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

        if (0 !== $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }
}