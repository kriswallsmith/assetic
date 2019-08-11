<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
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

    public function __construct($tscBin = '/usr/bin/tsc', $nodeBin = null)
    {
        $this->tscBin = $tscBin;
        $this->nodeBin = $nodeBin;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $args = $this->nodeBin
            ? array($this->nodeBin, $this->tscBin)
            : array($this->tscBin);

        if ($sourcePath = $asset->getSourcePath()) {
            $templateName = basename($sourcePath);
        } else {
            $templateName = 'asset';
        }

        $inputDirPath = FilesystemUtils::createThrowAwayDirectory('typescript_in');
        $inputPath = $inputDirPath.DIRECTORY_SEPARATOR.$templateName.'.ts';
        $outputPath = FilesystemUtils::createTemporaryFile('typescript_out');

        file_put_contents($inputPath, $asset->getContent());

        $args[] = $inputPath;
        $args[] = '--out';
        $args[] = $outputPath;


        $process = $this->createProcess($args);
        $code = $process->run();
        unlink($inputPath);
        rmdir($inputDirPath);

        if (0 !== $code) {
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
