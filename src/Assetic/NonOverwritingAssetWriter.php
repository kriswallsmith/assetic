<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic;

use Assetic\Asset\AssetInterface;
use Assetic\Util\VarUtils;

/**
 * Writes assets to the filesystem only when an asset is outdated.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class NonOverwritingAssetWriter extends AssetWriter
{
    public function writeAsset(AssetInterface $asset)
    {
        foreach (VarUtils::getCombinations($asset->getVars(), $this->values) as $combination) {
            $asset->setValues($combination);

            $target = $this->dir.'/'.VarUtils::resolve(
                $asset->getTargetPath(),
                $asset->getVars(),
                $asset->getValues()
            );

            if (!file_exists($target) || filemtime($target) < $asset->getLastModified()) {
                static::write(
                    $target,
                    $asset->dump()
                );
            }
        }
    }
}
