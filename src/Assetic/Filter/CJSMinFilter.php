<?php

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Runs assets through the C jsmin binary.
 *
 * @link http://javascript.crockford.com/jsmin.html
 * @author Felix Yeung
 */
class CJSMinFilter implements FilterInterface
{
    private $jsminBin;

    public function __construct($jsminBin = '/usr/bin/jsmin')
    {
        $this->jsminBin = $jsminBin;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $tmpAssetFile = tempnam(sys_get_temp_dir(), 'assetic_cjsmin_');
        file_put_contents($tmpAssetFile, $asset->getContent());

        $process = new Process($this->jsminBin . ' < ' . $tmpAssetFile);
        $process->run();

        $exitCode = $process->getExitCode();

        if (0 !== $exitCode) {
            if (127 === $exitCode) {
                throw new \RuntimeException('jsmin binary not found.');
            }

            $filterException = new FilterException($process->getErrorOutput(), $exitCode);
            $filterException->setInput($tmpAssetFile);

            throw $filterException;
        }

        $asset->setContent($process->getOutput());

        unlink($tmpAssetFile);
    }
}
