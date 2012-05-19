<?php

/*
 * This file is part of Assetic, an OpenSky project.
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
    private $isRegex;
    private $delegate;

    public function __construct($mimeType, ProcessorInterface $delegate)
    {
        if (0 === strpos($mimeType, '/')) {
            $this->mimeType = $mimeType;
            $this->isRegex = true;
        } elseif (false !== strpos($mimeType, '*')) {
            $this->mimeType = '#^'.str_replace('*', '.*', $mimeType).'$#';
            $this->isRegex = true;
        } else {
            $this->mimeType = $mimeType;
            $this->isRegex = false;
        }

        $this->delegate = $delegate;
    }

    public function process(AssetInterface $asset, Context $context)
    {
        $mimeType = $asset->getAttribute('mime_type');

        if ($this->isRegex) {
            $match = 0 < preg_match($this->mimeType, $mimeType);
        } else {
            $match = $this->mimeType === $mimeType;
        }

        if ($match) {
            $this->delegate->process($asset, $context);
        }
    }
}
