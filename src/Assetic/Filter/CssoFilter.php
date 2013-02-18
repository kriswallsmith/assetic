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
use Assetic\Filter\BaseCssFilter;

/**
 * Class CssoFilter
 *
 * Occupies the csso filter to minify css.
 * @author Mario Mueller <mario.mueller@trivago.com>
 */
class CssoFilter extends BaseProcessFilter
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

    /**
     * Filters an asset after it has been loaded.
     *
     * @param AssetInterface $asset An asset
     */
    public function filterLoad(AssetInterface $asset)
    {

    }

    /**
     * @param \Assetic\Asset\AssetInterface $asset
     * @return string
     * @throws \RuntimeException
     * @throws FilterException
     */
    public function filterDump(AssetInterface $asset)
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
        $pb->add('-i')->add($input)->add('-o')->add($output);

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

        $asset->setContent(file_get_contents($output));
        unlink($output);
    }
}
