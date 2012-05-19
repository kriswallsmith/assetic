<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\MimeType\Core\Processor;

use Assetic\Extension\Core\Processor\MimeTypeProcessor;

class MimeTypeProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldRunDelegateOnMatch()
    {
        $delegate = $this->getMock('Assetic\Extension\Core\Processor\ProcessorInterface');
        $asset = $this->getMock('Assetic\Asset\AssetInterface');

        $asset->expects($this->any())
            ->method('getAttribute')
            ->with('mime_type')
            ->will($this->returnValue('testing123'));
        $delegate->expects($this->once())
            ->method('process')
            ->with($asset);

        $processor = new MimeTypeProcessor('testing123', $delegate);
        $processor->process($asset);
    }

    /**
     * @test
     */
    public function shouldSkipDelegateOnMismatch()
    {
        $delegate = $this->getMock('Assetic\Extension\Core\Processor\ProcessorInterface');
        $asset = $this->getMock('Assetic\Asset\AssetInterface');

        $asset->expects($this->any())
            ->method('getAttribute')
            ->with('mime_type')
            ->will($this->returnValue('testing123'));
        $delegate->expects($this->never())
            ->method('process');

        $processor = new MimeTypeProcessor('testing456', $delegate);
        $processor->process($asset);
    }
}
