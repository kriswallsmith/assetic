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
        $commandline =array($this->jpegoptimBin);

        if ($this->stripAll) {
            array_push($commandline, '--strip-all');
        }

        if ($this->max) {
            array_push($commandline, '--max='.$this->max);
        }

        array_push($commandline, $input = FilesystemUtils::createTemporaryFile('jpegoptim'));
        file_put_contents($input, $asset->getContent());

        $proc = new Process($commandline);
        $proc->run();

        if (false !== strpos($proc->getOutput(), 'ERROR')) {
            unlink($input);
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent(file_get_contents($input));

        unlink($input);
    }
}
