<?php

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * Compiles JSX (for use with React) into JavaScript.
 *
 * @link http://facebook.github.io/react/docs/jsx-in-depth.html
 * @author Douglas Greenshields <dgreenshields@gmail.com>
 */
class ReactJsxFilter extends BaseNodeFilter
{
    private $jsxBin;
    private $nodeBin;

    public function __construct($jsxBin = '/usr/bin/jsx', $nodeBin = null)
    {
        $this->jsxBin = $jsxBin;
        $this->nodeBin = $nodeBin;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $builder = $this->createProcessBuilder($this->nodeBin
            ? array($this->nodeBin, $this->jsxBin)
            : array($this->jsxBin));

        $inputDir = FilesystemUtils::createThrowAwayDirectory('jsx_in');
        $inputFile = $inputDir.DIRECTORY_SEPARATOR.'asset.js';
        $outputDir = FilesystemUtils::createThrowAwayDirectory('jsx_out');
        $outputFile = $outputDir.DIRECTORY_SEPARATOR.'asset.js';

        // create the asset file
        file_put_contents($inputFile, $asset->getContent());

        $builder
            ->add($inputDir)
            ->add($outputDir)
            ->add('--no-cache-dir')
        ;

        $proc = $builder->getProcess();
        $code = $proc->run();

        // remove the input directory and asset file
        unlink($inputFile);
        rmdir($inputDir);

        if (0 !== $code) {
            if (file_exists($outputFile)) {
                unlink($outputFile);
            }

            if (file_exists($outputDir)) {
                rmdir($outputDir);
            }

            throw FilterException::fromProcess($proc);
        }

        $asset->setContent(file_get_contents($outputFile));

        // remove the output directory and processed asset file
        unlink($outputFile);
        rmdir($outputDir);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
