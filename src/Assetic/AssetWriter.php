<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic;

use Assetic\Asset\AssetInterface;

/**
 * Writes assets to the filesystem.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetWriter
{
    private $dir;

    /**
     * Constructor.
     *
     * @param string $dir The base web directory
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function writeManagerAssets(AssetManager $am)
    {
        foreach ($am->all() as $asset) {
            $this->writeAsset($asset);
        }
    }

    public function writeAsset(AssetInterface $asset)
    {
        static::write($this->dir . '/' . $asset->getTargetUrl(), $asset->dump());
    }

    static protected function write($path, $contents)
    {
        if (!is_dir($dir = dirname($path))) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($path, $contents);
    }
}
