<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

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
        $args = $this->nodeBin
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

        $args[] = $inputPath;
        $args[] = '-f';
        $args[] = $outputPath;

        if ($this->minimize) {
            $args[] = '--min';
        }

        if ($this->simple) {
            $args[] = '--simple';
        }

        $process = $this->createProcess($args);

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
}
