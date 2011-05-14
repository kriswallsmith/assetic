<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
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
        foreach ($am->getNames() as $name) {
            $this->writeAsset($am->get($name));
        }
    }

    public function writeAsset(AssetInterface $asset)
    {
        static::write($this->dir . '/' . $asset->getTargetPath(), $asset->dump());
    }

    static protected function write($path, $contents)
    {
        if (!is_dir($dir = dirname($path)) && false === @mkdir($dir, 0777, true)) {
            throw new \RuntimeException('Unable to create directory '.$dir);
        }

        if (false === @file_put_contents($path, $contents)) {
            throw new \RuntimeException('Unable to write file '.$path);
        }
    }
}
