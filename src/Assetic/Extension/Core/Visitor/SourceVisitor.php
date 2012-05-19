<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core\Visitor;

use Assetic\Asset\AbstractAssetVisitor;
use Assetic\Asset\AssetInterface;
use Assetic\Extension\Core\Source\Detector\DetectorInterface;
use Assetic\Extension\Core\Source\Finder\FinderInterface;

/**
 * The source loader is responsible for loading values from an asset's source.
 */
class SourceVisitor extends AbstractAssetVisitor
{
    private $finder;
    private $detector;

    public function __construct(FinderInterface $finder, DetectorInterface $detector)
    {
        $this->finder = $finder;
        $this->detector = $detector;

        parent::__construct(128);
    }

    protected function enterAsset(AssetInterface $asset)
    {
        if (!$asset->getAttribute('logical_path') || $asset->getAttribute('source_loaded')) {
            return $asset;
        }

        $asset->setAttribute('source_loaded', true);

        if ($source = $this->finder->findByLogicalPath($asset->getAttribute('logical_path'))) {
            $asset->setAttribute('path', $source->getPath());
            $asset->setAttribute('content', $source->getContent());
            $asset->setAttribute('mtime', $source->getLastModified());
            $asset->setAttribute('extensions', $source->getExtensions());
            $asset->setAttribute('mime_type', $this->detector->detectMimeType($source));
        }

        return $asset;
    }
}
