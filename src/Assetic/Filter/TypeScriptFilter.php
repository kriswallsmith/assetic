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
    private $useOut;

    public function __construct($tscBin = '/usr/bin/tsc', $nodeBin = null, $useOut = true)
    {
        $this->tscBin = $tscBin;
        $this->nodeBin = $nodeBin;
        $this->useOut = $useOut;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder($this->nodeBin
            ? array($this->nodeBin, $this->tscBin)
            : array($this->tscBin));

        $inputDirPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('input_dir');
        mkdir($inputDirPath);

        if (null !== $asset->getSourcePath()) {
            // Swap out reference path for the appropriate value.
            $referenceBasePath = '..'.DIRECTORY_SEPARATOR.'..'.dirname(($asset->getSourceRoot() ? $asset->getSourceRoot() . DIRECTORY_SEPARATOR : '') . $asset->getSourcePath());
            $asset->setContent(preg_replace('/\/\/\/\s*<\s*reference\s*path=[\'"]([^\'^"]*)[\'"]\s*\/>/', "/// <reference path='" . $referenceBasePath.DIRECTORY_SEPARATOR.'$1' . "'/>", $asset->getContent()));
        }

        $inputPath = $inputDirPath.DIRECTORY_SEPARATOR.uniqid('ts').'.ts';
        file_put_contents($inputPath, $asset->getContent());

        if ($this->useOut) {
            $outputPath = tempnam(sys_get_temp_dir(), 'output').'.js';
            $pb->add($inputPath)->add('--out')->add($outputPath);
        } else {
            $outputPath = preg_replace('/\.ts$/', '.js', $inputPath);
            $pb->add($inputPath);
        }

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
        unlink($inputPath);
        rmdir($inputDirPath);

        $asset->setContent($compiledJs);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
