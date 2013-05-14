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
 * Compiles TypeScript into JavaScript.
 *
 * @link http://www.typescriptlang.org/
 * @author Jarrod Nettles <jarrod.nettles@icloud.com>
 */
class TypeScriptFilter extends BaseNodeFilter
{
    private $tscBin;
    private $nodeBin;

    public function __construct($tscBin = '/usr/bin/tsc', $nodeBin = null)
    {
        $this->tscBin = $tscBin;
        $this->nodeBin = $nodeBin;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder($this->nodeBin
            ? array($this->nodeBin, $this->tscBin)
            : array($this->tscBin));

        $inputPath = $asset->getSourceRoot() . '/' . $asset->getSourcePath();
        $outputPath = tempnam(sys_get_temp_dir(), 'output');
        
        $pb->add($inputPath)->add('--out')->add($outputPath);

        $proc = $pb->getProcess();
        $code = $proc->run();

        if (0 !== $code) {
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        if (!file_exists($outputPath)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $compiledJs = file_get_contents($outputPath);
        unlink($outputPath);

        $asset->setContent($compiledJs);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
