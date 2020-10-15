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
 * Runs assets through OptiPNG.
 *
 * @link   http://optipng.sourceforge.net/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class OptiPngFilter extends BaseProcessFilter
{
    private $optipngBin;
    private $level;

    /**
     * Constructor.
     *
     * @param string $optipngBin Path to the optipng binary
     */
    public function __construct($optipngBin = '/usr/bin/optipng')
    {
        $this->optipngBin = $optipngBin;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $commandline =array($this->optipngBin);

        if (null !== $this->level) {
            array_push($commandline, '-o', $this->level);
        }

        array_push($commandline, '-out', $output = FilesystemUtils::createTemporaryFile('optipng_out'));
        unlink($output);

        array_push($commandline, $input = FilesystemUtils::createTemporaryFile('optinpg_in'));
        file_put_contents($input, $asset->getContent());

        $proc = new Process($commandline);
        $code = $proc->run();

        if (0 !== $code) {
            unlink($input);
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent(file_get_contents($output));

        unlink($input);
        unlink($output);
    }
}
