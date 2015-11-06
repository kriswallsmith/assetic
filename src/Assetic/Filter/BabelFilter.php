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

/**
 * Compiles ECMAScript2015 into ECMAScript5.
 * Based on the TypeScript Filter by Jarrod Nettles
 *
 * @link https://babeljs.io/
 * @author Jan Willem Owen <janwowen@mailbox.org>
 */
class BabelFilter extends BaseNodeFilter
{
    private $babelBin;
    private $nodeBin;

    public function __construct($babelBin = '/usr/bin/babel', $nodeBin = null)
    {
        $this->babelBin = $babelBin;
        $this->nodeBin = $nodeBin;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder($this->nodeBin
            ? array($this->nodeBin, $this->babelBin)
            : array($this->babelBin));

        if ($sourcePath = $asset->getSourcePath()) {
            $templateName = basename($sourcePath);
        } else {
            $templateName = 'asset';
        }

        $inputDirPath = FilesystemUtils::createThrowAwayDirectory('babel_in');
        $inputPath = $inputDirPath.DIRECTORY_SEPARATOR.$templateName.'.js';
        $outputPath = FilesystemUtils::createTemporaryFile('babel_out');

        file_put_contents($inputPath, $asset->getContent());

        $pb->add($inputPath)->add('--out-file')->add($outputPath);

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($inputPath);
        rmdir($inputDirPath);

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
