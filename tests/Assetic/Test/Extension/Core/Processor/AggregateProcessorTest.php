<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Core\Processor;

use Assetic\Extension\Core\Processor\AggregateProcessor;

class AggregateProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldRunDelegates()
    {
        $delegate = $this->getMock('Assetic\Extension\Core\Processor\ProcessorInterface');
        $asset = $this->getMock('Assetic\Asset\AssetInterface');

        $delegate->expects($this->once())
            ->method('process')
            ->with($asset);

        $processor = new AggregateProcessor(array($delegate));
        $processor->process($asset);
    }
}
