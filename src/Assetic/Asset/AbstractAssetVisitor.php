<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Tree\NodeInterface;
use Assetic\Tree\VisitorInterface;

abstract class AbstractAssetVisitor implements VisitorInterface
{
    private $priority;

    public function __construct($priority = 0)
    {
        $this->priority = $priority;
    }

    public function enter(NodeInterface $node)
    {
        if (!$node instanceof AssetInterface) {
            throw new \InvalidArgumentException('Node is not an asset');
        }

        return $this->enterAsset($node);
    }

    public function leave(NodeInterface $node)
    {
        if (!$node instanceof AssetInterface) {
            throw new \InvalidArgumentException('Node is not an asset');
        }

        return $this->leaveAsset($node);
    }

    public function getPriority()
    {
        return $this->priority;
    }

    protected function enterAsset(AssetInterface $asset)
    {
        return $asset;
    }

    protected function leaveAsset(AssetInterface $asset)
    {
        return $asset;
    }
}
