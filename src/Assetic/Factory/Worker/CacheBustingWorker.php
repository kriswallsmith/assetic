<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Worker;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Factory\LazyAssetManager;

/**
 * Adds cache busting code
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CacheBustingWorker implements WorkerInterface
{
    const STRATEGY_CONTENT = 1;
    const STRATEGY_MODIFICATION = 2;

    protected $am;
    private $separator;
    private $strategy;

    public function __construct(LazyAssetManager $am, $separator = '-', $strategy = self::STRATEGY_CONTENT)
    {
        $this->am = $am;
        $this->separator = $separator;
        $this->strategy = $strategy;
    }

    public function process(AssetInterface $asset)
    {
        if (!$path = $asset->getTargetPath()) {
            // no path to work with
            return;
        }

        if (!$search = pathinfo($path, PATHINFO_EXTENSION)) {
            // nothing to replace
            return;
        }

        $replace = $this->separator.$this->getHash($asset).'.'.$search;
        if (preg_match('/'.preg_quote($replace, '/').'$/', $path)) {
            // already replaced
            return;
        }

        $asset->setTargetPath(
            preg_replace('/\.'.preg_quote($search, '/').'$/', $replace, $path)
        );
    }

    /**
     * Get hash for Asset based on content
     *
     * @param AssetInterface $asset
     *
     * @return string
     */
    protected function getHashForAsset(AssetInterface $asset)
    {
        $path = implode('/', [$asset->getSourceRoot(), $asset->getSourcePath()]);
        $content = file_get_contents($path);

        return md5($content);
    }

    protected function getHash(AssetInterface $asset)
    {
        $hash = hash_init('sha1');

        switch($this->strategy) {
            case self::STRATEGY_CONTENT:
                if ($asset instanceof AssetCollectionInterface) {
                    foreach ($asset as $leaf) {
                        hash_update($hash, $this->getHashForAsset($leaf));
                    }
                } else {
                    hash_update($hash, $this->getHashForAsset($asset));
                }
                break;

            case self::STRATEGY_MODIFICATION:
                hash_update($hash, $this->am->getLastModified($asset));

                if ($asset instanceof AssetCollectionInterface) {
                    foreach ($asset as $i => $leaf) {
                        $sourcePath = $leaf->getSourcePath();
                        hash_update($hash, $sourcePath ?: $i);
                    }
                }
                break;
        }

        return substr(hash_final($hash), 0, 7);
    }
}
