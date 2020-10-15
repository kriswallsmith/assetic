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
 * Compiles Handlebars templates into Javascript.
 *
 * @link http://handlebarsjs.com/
 * @author Keyvan Akbary <keyvan@funddy.com>
 */
class HandlebarsFilter extends BaseNodeFilter
{
    private $handlebarsBin;
    private $nodeBin;

    private $minimize = false;
    private $simple = false;

    public function __construct($handlebarsBin = '/usr/bin/handlebars', $nodeBin = null)
    {
        $this->handlebarsBin = $handlebarsBin;
        $this->nodeBin = $nodeBin;
    }

    public function setMinimize($minimize)
    {
        $this->minimize = $minimize;
    }

    public function setSimple($simple)
    {
        $this->simple = $simple;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $commandline = $this->nodeBin
            ? array($this->nodeBin, $this->handlebarsBin)
            : array($this->handlebarsBin);

        if ($sourcePath = $asset->getSourcePath()) {
            $templateName = basename($sourcePath);
        } else {
            throw new \LogicException('The handlebars filter requires that assets have a source path set');
        }

        $inputDirPath = FilesystemUtils::createThrowAwayDirectory('handlebars_in');
        $inputPath = $inputDirPath.DIRECTORY_SEPARATOR.$templateName;
        $outputPath = FilesystemUtils::createTemporaryFile('handlebars_out');

        file_put_contents($inputPath, $asset->getContent());

        array_push($commandline, $inputPath,'-f',$outputPath);

        if ($this->minimize) {
            array_push($commandline, '--min');
        }

        if ($this->simple) {
            array_push($commandline, '--simple');
        }

        $process = new Process($commandline);
        $returnCode = $process->run();

        unlink($inputPath);
        rmdir($inputDirPath);

        if (127 === $returnCode) {
            throw new \RuntimeException('Path to node executable could not be resolved.');
        }

        if (0 !== $returnCode) {
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
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
