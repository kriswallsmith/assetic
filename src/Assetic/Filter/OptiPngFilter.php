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
 * Runs assets through OptiPNG.
 *
 * @link   http://optipng.sourceforge.net/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class OptiPngFilter extends AbstractProcessFilter
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
        $options = array($this->optipngBin);

        if (null !== $this->level) {
            $options[] = '-o';
            $options[] = $this->level;
        }

        $options[] = '-out';
        $options[] = $output = tempnam(self::getTempDir(), 'assetic_optipng');
        unlink($output);

        $options[] = $input = tempnam(self::getTempDir(), 'assetic_optipng');
        file_put_contents($input, $asset->getContent());

        $process = $this->createProcess($options);
        $code = $process->run();
        unlink($input);
        if (0 < $code) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        $asset->setContent($process->getOutput());

        unlink($output);
    }
}
