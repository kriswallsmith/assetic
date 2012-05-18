<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core\Visitor;

use Assetic\Asset\AbstractAssetVisitor;
use Assetic\Asset\AssetInterface;

/**
 * Tries to figure out an asset's logical path.
 */
class LogicalPathVisitor extends AbstractAssetVisitor
{
    public function __construct()
    {
        parent::__construct(-128);
    }

    protected function enterAsset(AssetInterface $asset)
    {
        if ($asset->getAttribute('logical_path')) {
            return $asset;
        }

        $parent = $asset->getParent();
        if (!$parent || !$parentPath = $parent->getAttribute('logical_path')) {
            return $asset;
        }

        $revPath = $asset->getAttribute('parent.rev_path');
        if (!$revPath || !$this->isRelativeUrl($revPath)) {
            return $asset;
        }

        // good enough for now
        $asset->setAttribute('logical_path', dirname($parentPath).'/'.$revPath);

        return $asset;
    }

    private function isRelativeUrl($url)
    {
        // does not include :// and does not start with /
        return false === strpos($url, '://') && 0 !== strpos($url, '/');
    }
}
