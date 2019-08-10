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

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * Runs assets through Jpegoptim.
 *
 * @link   http://www.kokkonen.net/tjko/projects.html
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class JpegoptimFilter extends BaseProcessFilter
{
    private $jpegoptimBin;
    private $stripAll;
    private $max;

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

    public function setMax($max)
    {
        $this->max = $max;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $args = [$this->jpegoptimBin];

        if ($this->stripAll) {
            $args[] = '--strip-all';
        }

        if ($this->max) {
            $args[] = '--max=' . $this->max;
        }

        $input = FilesystemUtils::createTemporaryFileAndWrite('jpegoptim', $asset->getContent());

        $args[] = $input;

        $process = $this->createProcess($args);
        $process->run();

        if (false !== strpos($process->getOutput(), 'ERROR')) {
            unlink($input);
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        $asset->setContent(file_get_contents($input));

        unlink($input);
    }
}
