<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Runs assets through Jpegoptim.
 *
 * @link   http://www.kokkonen.net/tjko/projects.html
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class JpegoptimFilter extends AbstractProcessFilter
{
    private $jpegoptimBin;
    private $stripAll;

    /**
     * Constructor.
     *
     * @param string $jpegoptimBin Path to the jpegoptim binary
     */
    public function __construct($jpegoptimBin = '/usr/bin/jpegoptim')
    {
        $this->jpegoptimBin = $jpegoptimBin;
    }

    public function setStripAll($stripAll)
    {
        $this->stripAll = $stripAll;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $options = array($this->jpegoptimBin);

        if ($this->stripAll) {
            $options[] = '--strip-all';
        }

        $options[] = $input = tempnam(sys_get_temp_dir(), 'assetic_jpegoptim');
        file_put_contents($input, $asset->getContent());

        try {
            $this->runProcess($options);
        } catch (\RuntimeException $ex) {}

        if (false !== strpos($this->getProcessOutput(), 'ERROR')) {
            unlink($input);
            throw new \RuntimeException($this->getProcessOutput());
        }

        $asset->setContent(file_get_contents($input));

        unlink($input);
    }
}
