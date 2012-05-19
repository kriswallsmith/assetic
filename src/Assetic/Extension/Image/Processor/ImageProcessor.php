<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Image\Processor;

use Assetic\Asset\AssetInterface;
use Assetic\Extension\Core\Processor\Context;
use Assetic\Extension\Core\Processor\ProcessorInterface;
use Assetic\Extension\Image\ImageExtension;

class ImageProcessor implements ProcessorInterface
{
    private $extension;

    public function __construct(ImageExtension $extension)
    {
        $this->extension = $extension;
    }

    public function process(AssetInterface $asset, Context $context)
    {
        $mimeType = $asset->getAttribute('mime_type');

        if ($mimeType && $processor = $this->extension->getImageOptimizer($mimeType)) {
            $processor->process($asset, $context);
        }
    }
}
