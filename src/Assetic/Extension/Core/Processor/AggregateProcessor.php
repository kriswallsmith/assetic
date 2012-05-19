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
 * Runs many processors on an asset.
 */
class AggregateProcessor implements ProcessorInterface
{
    private $processors;

    public function __construct(array $processors = array())
    {
        $this->processors = array();
        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }
    }

    public function addProcessor(ProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    public function process(AssetInterface $asset)
    {
        foreach ($this->processors as $processor) {
            $processor->process($asset);
        }
    }
}
