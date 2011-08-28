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
use Assetic\Util\ProcessBuilder;

/**
 * Loads LESS files.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class LessFilter implements FilterInterface
{
    private $lesscBin;
    private $compress;

    /**
     * Constructor.
     *
     * @param string $lesscBin The path to the lessc binary
     */
    public function __construct($lesscBin = '/usr/bin/lessc')
    {
        $this->lesscBin = $lesscBin;
    }

    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = new ProcessBuilder();
        $pb
            ->inheritEnvironmentVariables()
            ->add($this->lesscBin)
        ;

        // compress
        if ($this->compress) {
            $pb->add('--compress');
        }

        // include path
        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();
        if ($root && $path) {
            $pb->add('-I'.dirname($root.DIRECTORY_SEPARATOR.$path));
        }

        // input
        $input = tempnam(sys_get_temp_dir(), 'assetic_less');
        $pb->add($input);
        file_put_contents($input, $asset->getContent());

        // output
        $output = $input.'.out';
        $pb->add($output);

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            throw new \RuntimeException($proc->getErrorOutput());
        } elseif (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $asset->setContent(file_get_contents($output));
        unlink($output);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
