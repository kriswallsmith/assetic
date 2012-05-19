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
use Assetic\Extension\Core\Processor\ProcessorInterface;

/**
 * Runs processors on each asset.
 */
class ProcessorVisitor extends AbstractAssetVisitor
{
    private $processor;

    public function __construct(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    protected function enterAsset(AssetInterface $asset)
    {
        $this->processor->process($asset);

        return $asset;
    }
}
