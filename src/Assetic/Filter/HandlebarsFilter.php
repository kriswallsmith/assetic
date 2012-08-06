<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Compiles Handlebars templates into Javascript.
 *
 * @link http://handlebarsjs.com/
 * @author Keyvan Akbary <keyvan@funddy.com>
 */
class HandlebarsFilter implements FilterInterface
{
    private $handlebarsPath;
    private $nodePath;

    private $minimize = false;
    private $simple = false;

    public function __construct($handlebarsPath = '/usr/bin/handlebars', $nodePath = null)
    {
        $this->handlebarsPath = $handlebarsPath;
        $this->nodePath = $nodePath;
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
        $executables = array();

        if ($this->nodePath !== null) {
            $executables[] = $this->nodePath;
        }

        $executables[] = $this->handlebarsPath;

        $processBuilder = new ProcessBuilder($executables);

        $templateName = basename($asset->getSourcePath());

        $inputDirPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('input_dir');
        $inputPath = $inputDirPath.DIRECTORY_SEPARATOR.$templateName;
        $outputPath = tempnam(sys_get_temp_dir(), 'output');

        mkdir($inputDirPath);
        file_put_contents($inputPath, $asset->getContent());

        $processBuilder->add($inputPath)->add('-f')->add($outputPath);

        if ($this->minimize) {
            $processBuilder->add('--min');
        }

        if ($this->simple) {
            $processBuilder->add('--simple');
        }

        $process = $processBuilder->getProcess();
        $returnCode = $process->run();

        unlink($inputPath);
        rmdir($inputDirPath);

        if (127 === $returnCode) {
            throw new \RuntimeException('Path to node executable could not be resolved.');
        }

        if (0 < $returnCode) {
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
