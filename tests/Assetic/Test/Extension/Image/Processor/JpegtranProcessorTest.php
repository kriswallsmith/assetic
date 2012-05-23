<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Image\Processor;

use Assetic\Extension\Image\Processor\JpegtranProcessor;

class JpegtranProcessorTest extends \PHPUnit_Framework_TestCase
{
    private $context;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Assetic\Extension\Core\Processor\Context')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        unset($this->context);
    }

    /**
     * @test
     */
    public function shouldIgnoreProcessedAssets()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');

        $this->context->expects($this->any())
            ->method('isAssetProcessedBy')
            ->with($asset, 'jpegtran')
            ->will($this->returnValue(true));
        $this->context->expects($this->never())
            ->method('markAssetProcessedBy');

        $processor = new JpegtranProcessor();
        $processor->process($asset, $this->context);
    }

    /**
     * @test
     */
    public function shouldIgnoreContentlessAssets()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');

        $this->context->expects($this->any())
            ->method('isAssetProcessedBy')
            ->with($asset, 'jpegtran')
            ->will($this->returnValue(false));
        $asset->expects($this->once())
            ->method('getAttribute')
            ->with('content')
            ->will($this->returnValue(null));
        $this->context->expects($this->never())
            ->method('markAssetProcessedBy');

        $processor = new JpegtranProcessor();
        $processor->process($asset, $this->context);
    }

    /**
     * @test
     */
    public function shouldProcessAsset()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');
        $pb = $this->getMockBuilder('Symfony\Component\Process\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $file = $this->getMockBuilder('Gaufrette\File')
            ->disableOriginalConstructor()
            ->getMock();
        $proc = $this->getMockBuilder('Symfony\Component\Process\Process')
            ->disableOriginalConstructor()
            ->getMock();

        $this->context->expects($this->any())
            ->method('isAssetProcessedBy')
            ->with($asset, 'jpegtran')
            ->will($this->returnValue(false));
        $asset->expects($this->once())
            ->method('getAttribute')
            ->with('content')
            ->will($this->returnValue('content123'));
        $this->context->expects($this->once())
            ->method('markAssetProcessedBy')
            ->with($asset, 'jpegtran');
        $this->context->expects($this->once())
            ->method('findExecutable')
            ->with('jpegtran')
            ->will($this->returnValue('jpegtran'));
        $this->context->expects($this->once())
            ->method('createProcessBuilder')
            ->with(array('jpegtran'))
            ->will($this->returnValue($pb));
        $pb->expects($this->at(0))
            ->method('add')
            ->with('-copy')
            ->will($this->returnSelf());
        $pb->expects($this->at(1))
            ->method('add')
            ->with('none');
        $pb->expects($this->at(2))
            ->method('add')
            ->with('-optimize');
        $pb->expects($this->at(3))
            ->method('add')
            ->with('-progressive');
        $pb->expects($this->at(4))
            ->method('add')
            ->with('-restart')
            ->will($this->returnSelf());
        $pb->expects($this->at(5))
            ->method('add')
            ->with('10');
        $this->context->expects($this->once())
            ->method('createTempFile')
            ->will($this->returnValue($file));
        $pb->expects($this->at(6))
            ->method('add')
            ->with(null); // unable to mock createTempFile setting this value
        $pb->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($proc));
        $proc->expects($this->once())
            ->method('run');
        $file->expects($this->once())
            ->method('delete');
        $proc->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));
        $proc->expects($this->once())
            ->method('getOutput')
            ->will($this->returnValue('optimized123'));
        $asset->expects($this->once())
            ->method('setAttribute')
            ->with('content', 'optimized123');

        $processor = new JpegtranProcessor(array(
            'copy' => 'none',
            'optimize' => true,
            'progressive' => true,
            'restart' => 10,
        ));
        $processor->process($asset, $this->context);
    }
}
