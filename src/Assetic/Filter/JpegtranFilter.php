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
 * Runs assets through jpegtran.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class JpegtranFilter implements FilterInterface
{
    const COPY_NONE = 'none';
    const COPY_COMMENTS = 'comments';
    const COPY_ALL = 'all';

    private $jpegtranBin;
    private $optimize;
    private $copy;
    private $progressive;
    private $restart;

    /**
     * Constructor.
     *
     * @param string $jpegtranBin Path to the jpegtran binary
     */
    public function __construct($jpegtranBin = '/usr/bin/jpegtran')
    {
        $this->jpegtranBin = $jpegtranBin;
    }

    public function setOptimize($optimize)
    {
        $this->optimize = $optimize;
    }

    public function setCopy($copy)
    {
        $this->copy = $copy;
    }

    public function setProgressive($progressive)
    {
        $this->progressive = $progressive;
    }

    public function setRestart($restart)
    {
        $this->restart = $restart;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $options = array($this->jpegtranBin);

        if ($this->optimize) {
            $options[] = '-optimize';
        }

        if ($this->copy) {
            $options[] = '-copy';
            $options[] = $this->copy;
        }

        if ($this->progressive) {
            $options[] = '-progressive';
        }

        if (null !== $this->restart) {
            $options[] = '-restart';
            $options[] = $this->restart;
        }

        $options[] = $input = tempnam(sys_get_temp_dir(), 'assetic_jpegtran');
        file_put_contents($input, $asset->getContent());

        $proc = new Process(implode(' ', array_map('escapeshellarg', $options)));
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }
}
