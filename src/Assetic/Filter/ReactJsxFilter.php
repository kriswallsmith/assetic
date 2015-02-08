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
        $inputDir = $this->createTempDir();
        $outputDir = $this->createTempDir();
        $inputMinusExtension = tempnam($inputDir, '');
        $inputFilePath = $inputMinusExtension . '.js';
        file_put_contents($inputFilePath, $asset->getContent());

        $pb = $this->createProcessBuilder($this->nodeBin
            ? array($this->nodeBin, $this->jsxBin)
            : array($this->jsxBin));

        $pb->add($inputDir)->add($outputDir)->add('--no-cache-dir');

        $proc = $pb->getProcess();
        $code = $proc->run();

        $file = new \SplFileInfo($inputFilePath);
        $filename = $file->getFilename();
        $outputFilePath = $outputDir . '/' . $filename;

        //clean up input temp files/dirs
        unlink($inputMinusExtension);
        unlink($inputFilePath);
        rmdir($inputDir);

        if (0 !== $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $output = file_get_contents($outputFilePath);
        $asset->setContent($output);

        //clean up output temp files/dirs
        unlink($outputFilePath);
        rmdir($outputDir);
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    private function createTempDir()
    {
        $dirName = tempnam(sys_get_temp_dir(), 'assetic_react_jsx');
        if (file_exists($dirName)) {
            $this->deleteDir($dirName);
        }
        mkdir($dirName);

        return $dirName;
    }

    private function deleteDir($dirName)
    {
        foreach (glob($dirName . '/*') as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
                continue;
            }
            unlink($file);
        }
        unlink($dirName);
    }
}
