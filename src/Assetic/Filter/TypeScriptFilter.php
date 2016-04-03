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
 * Compiles TypeScript into JavaScript.
 *
 * @link http://www.typescriptlang.org/
 * @author Jarrod Nettles <jarrod.nettles@icloud.com>
 */
class TypeScriptFilter extends BaseNodeFilter
{
    private $tscBin;
    private $nodeBin;

    /**
     * @var bool
     */
    private $useRealPath = false;

    public function __construct($tscBin = '/usr/bin/tsc', $nodeBin = null, array $options = array())
    {
        $this->tscBin = $tscBin;
        $this->nodeBin = $nodeBin;
        if (isset($options['use_real_path'])) {
            $this->useRealPath = $options['use_real_path'] == true;
        }
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder($this->nodeBin
            ? array($this->nodeBin, $this->tscBin)
            : array($this->tscBin));

        if ($sourcePath = $asset->getSourcePath()) {
            $templateName = basename($sourcePath);
        } else {
            $templateName = 'asset';
        }

        $inputDirPath = FilesystemUtils::createThrowAwayDirectory('typescript_in');
        $inputPath = $inputDirPath.DIRECTORY_SEPARATOR.$templateName.'.ts';
        $outputPath = FilesystemUtils::createTemporaryFile('typescript_out');

        file_put_contents($inputPath, $this->getAssetContent($asset));

        $pb->add($inputPath)->add('--out')->add($outputPath);

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

    private function getAssetContent(AssetInterface $asset)
    {
        if ($this->useRealPath && $asset->getSourcePath() && $asset->getSourceRoot()) {
            $pathInfo = pathinfo($asset->getSourcePath());
            $dir = $asset->getSourceRoot() . DIRECTORY_SEPARATOR . $pathInfo['dirname'];

            $func = function ($matches) use ($dir) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $matches[2]);
                if ($path === false) {
                    $path = $matches[2];
                }

                return $matches[1] . $path;
            };

            return preg_replace_callback('|(\s*/{3}\s*<reference\s+path=")([^"]+)|', $func, $asset->getContent());
        }

        return $asset->getContent();
    }
}
