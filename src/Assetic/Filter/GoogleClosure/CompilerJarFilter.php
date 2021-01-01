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

        $command = array_merge(
            [$this->javaPath],
            $is64bit
                ? ['-server', '-XX:+TieredCompilation']
                : ['-client', '-d32'],
            ['-jar', $this->jarPath]
        );

        if (null !== $this->compilationLevel) {
            array_push($command, '--compilation_level', $this->compilationLevel);
        }

        if (null !== $this->jsExterns) {
            $cleanup[] = $externs = FilesystemUtils::createTemporaryFile('google_closure');
            file_put_contents($externs, $this->jsExterns);
            array_push($command, '--externs', $externs);
        }

        if (null !== $this->externsUrl) {
            $cleanup[] = $externs = FilesystemUtils::createTemporaryFile('google_closure');
            file_put_contents($externs, file_get_contents($this->externsUrl));
            array_push($command, '--externs', $externs);
        }

        if (null !== $this->excludeDefaultExterns) {
            $command[] = '--use_only_custom_externs';
        }

        if (null !== $this->formatting) {
            array_push($command, '--formatting', $this->formatting);
        }

        if (null !== $this->useClosureLibrary) {
            $command[] = '--manage_closure_dependencies';
        }

        if (null !== $this->warningLevel) {
            array_push($command, '--warning_level', $this->warningLevel);
        }

        if (null !== $this->language) {
            array_push($command, '--language_in', $this->language);
        }

        if (null !== $this->flagFile) {
            array_push($command, '--flagfile', $this->flagFile);
        }

        array_push($command, '--js', $cleanup[] = $input = FilesystemUtils::createTemporaryFile('google_closure'));
        file_put_contents($input, $asset->getContent());

        $pb = new Process($command);

        if (null !== $this->timeout) {
            $pb->setTimeout($this->timeout);
        }

        $code = $pb->run();
        array_map('unlink', $cleanup);

        if (0 !== $code) {
            throw FilterException::fromProcess($pb)->setInput($asset->getContent());
        }

        $asset->setContent($pb->getOutput());
    }
}
