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

class ChainDetector implements DetectorInterface
{
    private $detectors;

    public function __construct(array $detectors = array())
    {
        $this->detectors = array();
        foreach ($detectors as $detector) {
            $this->addDetector($detector);
        }
    }

    public function addDetector(DetectorInterface $detector)
    {
        $this->detectors[] = $detector;
    }

    public function detectMimeType(SourceInterface $source)
    {
        foreach ($this->detectors as $detector) {
            if ($mimeType = $detector->detectMimeType($source)) {
                return $mimeType;
            }
        }
    }
}
