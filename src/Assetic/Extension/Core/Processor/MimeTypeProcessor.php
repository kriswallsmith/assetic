<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core\Processor;

use Assetic\Asset\AssetInterface;

/**
 * Wraps a processor with a MIME type check.
 */
class MimeTypeProcessor implements ProcessorInterface
{
    private $mimeType;
    private $delegate;

    public function __construct($mimeType, ProcessorInterface $delegate)
    {
        $this->mimeType = $mimeType;
        $this->delegate = $delegate;
    }

    public function process(AssetInterface $asset)
    {
        if ($this->mimeType === $asset->getAttribute('mime_type')) {
            $this->delegate->process($asset);
        }
    }
}
