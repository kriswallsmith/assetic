<?php

namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
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
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath = '/usr/bin/jsx';

    /**
     * {@inheritDoc}
     */
    protected function getInputPath(string $input)
    {
        $path = FilesystemUtils::createThrowAwayDirectory('jsx_in');
        file_put_contents($path . '/asset.js', $input);
        return $path;
    }

    /**
     * {@inheritDoc}
     */
    protected function getOutputPath()
    {
        return FilesystemUtils::createThrowAwayDirectory('jsx_out');
    }

    /**
     * {@inheritDoc}
     */
    protected function getOutput()
    {
        return file_get_contents($this->outputPath . '/asset.js');
    }

    /**
     * {@inheritDoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $args = [
            '{INPUT}',
            '{OUTPUT}',
            '--no-cache-dir',
        ];

        $result = $this->runProcess($asset->getContent(), $args);
        $asset->setContent($result);
    }
}
