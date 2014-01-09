<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Factory\Worker;

use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;

class CacheBustingWorkerTest extends \PHPUnit_Framework_TestCase
{
    private $worker;

    protected function setUp()
    {
        $this->worker = new CacheBustingWorker();
    }

    protected function tearDown()
    {
        $this->worker = null;
    }

    /**
     * @test
     */
    public function shouldApplyHash()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');
        $factory = $this->getMockBuilder('Assetic\Factory\AssetFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue('css/main.css'));
        $factory->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->once())
            ->method('setTargetPath')
            ->with($this->logicalAnd(
                $this->stringStartsWith('css/main-'),
                $this->stringEndsWith('.css')
            ));

        $this->worker->process($asset, $factory);
    }

    /**
     * @test
     */
    public function shouldApplyConsistentHash()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');
        $factory = $this->getMockBuilder('Assetic\Factory\AssetFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $paths = array();

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue('css/main.css'));
        $factory->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->exactly(2))
            ->method('setTargetPath')
            ->will($this->returnCallback(function($path) use(& $paths) {
                $paths[] = $path;
            }));

        $this->worker->process($asset, $factory);
        $this->worker->process($asset, $factory);

        $this->assertCount(2, $paths);
        $this->assertCount(1, array_unique($paths));
    }

    /**
     * @test
     */
    public function shouldNotReapplyHash()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');
        $factory = $this->getMockBuilder('Assetic\Factory\AssetFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $path = null;

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnCallback(function() use(& $path) {
                return $path ?: 'css/main.css';
            }));
        $factory->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->once())
            ->method('setTargetPath')
            ->will($this->returnCallback(function($arg) use(& $path) {
                $path = $arg;
            }));

        $this->worker->process($asset, $factory);
        $this->worker->process($asset, $factory);
    }
}
