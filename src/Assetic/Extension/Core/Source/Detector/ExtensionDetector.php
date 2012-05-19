<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core\Source\Detector;

use Assetic\Extension\Core\Source\SourceInterface;

/**
 * Determines MIME type based on an extension map.
 */
class ExtensionDetector implements DetectorInterface
{
    private $map;

    public function __construct(array $map = array())
    {
        $this->map = $map;
    }

    public function detectMimeType(SourceInterface $source)
    {
        $extensions = $source->getExtensions();
        $extension = array_pop($extensions);
        if ($extension && isset($this->map[$extension])) {
            return $this->map[$extension];
        }
    }
}
