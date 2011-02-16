<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Runs assets through Sprockets.
 *
 * @link   http://getsprockets.org/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class SprocketsFilter implements FilterInterface
{
    private $baseDir;
    private $sprocketizePath;
    private $includeDirs = array();
    private $assetRoot;

    public function __construct($baseDir, $sprocketizePath = '/usr/bin/sprocketize')
    {
        $this->baseDir = $baseDir;
        $this->sprocketizePath = $sprocketizePath;
    }

    public function addIncludeDir($directory)
    {
        $this->includeDirs[] = $directory;
    }

    public function setAssetRoot($assetRoot)
    {
        $this->assetRoot = $assetRoot;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $sourceUrl = $asset->getSourceUrl();
        if (!$sourceUrl || false !== strpos($sourceUrl, '://')) {
            return;
        }

        $options = array($this->sprocketizePath);

        foreach ($this->includeDirs as $directory) {
            $options[] = '-I';
            $options[] = $directory;
        }

        if (null !== $this->assetRoot) {
            $options[] = '-a';
            $options[] = $this->assetRoot;
        }

        // hack in a temporary file sibling
        $options[] = $input = dirname($this->baseDir.'/'.$sourceUrl).'/.'.rand(11111, 99999).'-'.basename($sourceUrl);
        $tmp = tempnam(sys_get_temp_dir(), 'assetic_sprockets');
        file_put_contents($tmp, $asset->getContent());
        rename($tmp, $input);

        $proc = new Process(implode(' ', array_map('escapeshellarg', $options)));
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
