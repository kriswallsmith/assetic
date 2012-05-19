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
 * Wraps a processor with an extension check.
 */
class ExtensionProcessor implements ProcessorInterface
{
    private $extension;
    private $delegate;

    public function __construct($extension, ProcessorInterface $delegate)
    {
        $this->extension = $extension;
        $this->delegate = $delegate;
    }

    public function process(AssetInterface $asset)
    {
        $extensions = $asset->getAttribute('extensions', array());
        $extension = array_pop($extensions);

        if ($extension && $this->extension === $extension) {
            $this->delegate->process($asset);
        }
    }
}
