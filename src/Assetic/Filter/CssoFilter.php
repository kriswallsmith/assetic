<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter\Yui;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Filter\BaseCssFilter;
use Assetic\Filter\BaseProcessFilter;

/**
 * Base YUI compressor filter.
 *
 * @link http://developer.yahoo.com/yui/compressor/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
abstract class Csso extends BaseCssFilter
{
    private $cssoPath;
    private $noRestructure;

    public function __construct($cssoPath)
    {
        $this->cssoPath = $cssoPath;
    }

    public function setNoRestructure($noRestructure)
    {
        $this->noRestructure = $noRestructure;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder(array($this->cssoPath));

        if (null !== $this->noRestructure) {
            $pb->add('--restructure-off');
        }

        // input and output files
        $tempDir = realpath(sys_get_temp_dir());
        $input = tempnam($tempDir, 'CSSO-IN-');
        $output = tempnam($tempDir, 'CSSO-OUT-');
        file_put_contents($input, $asset->getContent());
        $pb->add($input)->add($output);

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            throw FilterException::fromProcess($proc)->setInput($input);
        }

        if (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $retval = file_get_contents($output);
        unlink($output);

        return $retval;
    }
}
