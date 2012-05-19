<?php

/*
 * This file is part of Assetic, an OpenSky project.
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
class FileinfoDetector implements DetectorInterface
{
    public function detectMimeType(SourceInterface $source)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($source->getContent());
    }
}
