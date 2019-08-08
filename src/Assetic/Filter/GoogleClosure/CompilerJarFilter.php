<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter\GoogleClosure;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;
use Symfony\Component\Process\Process;

/**
 * Filter for the Google Closure Compiler JAR.
 *
 * @link https://developers.google.com/closure/compiler/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CompilerJarFilter extends BaseCompilerFilter
{
    private $jarPath;
    private $javaPath;
    private $flagFile;

    public function __construct($jarPath, $javaPath = '/usr/bin/java')
    {
        $this->jarPath = $jarPath;
        $this->javaPath = $javaPath;
    }

    public function setFlagFile($flagFile)
    {
        $this->flagFile = $flagFile;
    }

    public function filterDump(AssetInterface $asset)
    {
        $is64bit = PHP_INT_SIZE === 8;
        $cleanup = array();

        $args = array_merge(
            array($this->javaPath),
            $is64bit
                ? array('-server', '-XX:+TieredCompilation')
                : array('-client', '-d32'),
            array('-jar', $this->jarPath)
        );

        if (null !== $this->compilationLevel) {
            $args[] = '--compilation_level';
            $args[] = $this->compilationLevel;
        }

        if (null !== $this->jsExterns) {
            $cleanup[] = $externs = FilesystemUtils::createTemporaryFile('google_closure');
            file_put_contents($externs, $this->jsExterns);
            $args[] = '--externs';
            $args[] = $externs;
        }

        if (null !== $this->externsUrl) {
            $cleanup[] = $externs = FilesystemUtils::createTemporaryFile('google_closure');
            file_put_contents($externs, file_get_contents($this->externsUrl));
            $args[] = '--externs';
            $args[] = $externs;
        }

        if (null !== $this->excludeDefaultExterns) {
            $args[] = '--use_only_custom_externs';
        }

        if (null !== $this->formatting) {
            $args[] = '--formatting';
            $args[] = $this->formatting;
        }

        if (null !== $this->useClosureLibrary) {
            $args[] = '--manage_closure_dependencies';
        }

        if (null !== $this->warningLevel) {
            $args[] = '--warning_level';
            $args[] = $this->warningLevel;
        }

        if (null !== $this->language) {
            $args[] = '--language_in';
            $args[] = $this->language;
        }

        if (null !== $this->flagFile) {
            $args[] = '--flagfile';
            $args[] = $this->flagFile;
        }

        $args[] = '--js';
        $args[] = $cleanup[] = $input = FilesystemUtils::createTemporaryFile('google_closure');
        file_put_contents($input, $asset->getContent());

        $process = new Process($args);

        if (null !== $this->timeout) {
            $process->setTimeout($this->timeout);
        }

        $code = $process->run();
        array_map('unlink', $cleanup);

        if (0 !== $code) {
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        $asset->setContent($process->getOutput());
    }
}
