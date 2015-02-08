<?php

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;

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

        $inputDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('assetic_reactjsx_input');
        $inputFile = $inputDir.DIRECTORY_SEPARATOR.'asset.js';
        $outputDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('assetic_reactjsx_output');
        $outputFile = $outputDir.DIRECTORY_SEPARATOR.'asset.js';

        // create the input directory and asset file
        mkdir($inputDir);
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
