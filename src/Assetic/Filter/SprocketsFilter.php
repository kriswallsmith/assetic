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
    private $directory;
    private $includeDirs = array();
    private $assetRoot;

    public function __construct($baseDir, $sprocketizePath = '/usr/bin/sprocketize')
    {
        $this->baseDir = $baseDir;
        $this->sprocketizePath = $sprocketizePath;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
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

        // fixme: shouldn't the -C option do this?
        if (!$sourceUrl || false !== strpos($sourceUrl, '://')) {
            return;
        }

        $options = array($this->sprocketizePath);

        if (null !== $this->directory) {
            $options[] = '-C';
            $options[] = $this->directory;
        }

        foreach ($this->includeDirs as $directory) {
            $options[] = '-I';
            $options[] = $directory;
        }

        if (null !== $this->assetRoot) {
            $options[] = '-a';
            $options[] = $this->assetRoot;
        }

        // fixme: shouldn't the -C option do this?
        $tmp = tempnam(sys_get_temp_dir(), 'assetic_sprockets');
        file_put_contents($tmp, $asset->getContent());

        // create a file "sibling"
        $options[] = $input = $this->baseDir.'/'.dirname($sourceUrl).'/.'.rand(11111, 99999).'-'.basename($sourceUrl);
        rename($tmp, $input);

        // todo: check for a valid return code
        $output = shell_exec(implode(' ', array_map('escapeshellarg', $options)));

        unlink($input);

        $asset->setContent($output);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
