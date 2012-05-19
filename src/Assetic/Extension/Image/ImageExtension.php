<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Image;

use Assetic\AbstractExtension;
use Assetic\EnvironmentInterface;
use Assetic\Extension\Image\ImageProcessor;

class ImageExtension extends AbstractExtension
{
    private $processors;

    public function __construct()
    {
        $this->processors = array();
    }

    public function initialize(EnvironmentInterface $env)
    {
        $env->getExtension('core')
            ->registerMimeType('gif', 'image/gif')
            ->registerMimeType('jpeg', 'image/jpeg')
            ->registerMimeType('jpg', 'image/jpeg')
            ->registerMimeType('png', 'image/png')
            ->registerPostProcessor(new ImageProcessor($this), 'image/*')
        ;
    }

    public function registerImageOptimizer($mimeType, ProcessorInterface $processor)
    {
        $this->processors[$mimeType] = $processor;
    }

    public function getImageOptimizer($mimeType)
    {
        if (isset($this->processors[$mimeType])) {
            return $this->processors[$mimeType];
        }
    }

    public function getName()
    {
        return 'image';
    }
}
