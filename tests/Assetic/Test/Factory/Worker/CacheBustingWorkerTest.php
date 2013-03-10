<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Factory\Worker;

use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;

class CacheBustingWorkerTest extends \PHPUnit_Framework_TestCase
{
    private $am;
    private $worker;

    protected function setUp()
    {
        $this->am = $this->getMockBuilder('Assetic\Factory\LazyAssetManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->worker = new CacheBustingWorker($this->am);
    }

    protected function tearDown()
    {
        $this->am = null;
        $this->worker = null;
    }

    /**
     * @test
     */
    public function shouldApplyHash()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue('css/main.css'));
        $this->am->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->once())
            ->method('setTargetPath')
            ->with($this->logicalAnd(
                $this->stringStartsWith('css/main-'),
                $this->stringEndsWith('.css')
            ));

        $this->worker->process($asset);
    }

    /**
     * @test
     */
    public function shouldApplyConsistentHash()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');
        $paths = array();

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue('css/main.css'));
        $this->am->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->exactly(2))
            ->method('setTargetPath')
            ->will($this->returnCallback(function($path) use(& $paths) {
                $paths[] = $path;
            }));

        $this->worker->process($asset);
        $this->worker->process($asset);

        $this->assertCount(2, $paths);
        $this->assertCount(1, array_unique($paths));
    }

    /**
     * @test
     */
    public function shouldNotReapplyHash()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');
        $path = null;

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnCallback(function() use(& $path) {
                return $path ?: 'css/main.css';
            }));
        $this->am->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->once())
            ->method('setTargetPath')
            ->will($this->returnCallback(function($arg) use(& $path) {
                $path = $arg;
            }));

        $this->worker->process($asset);
        $this->worker->process($asset);
    }
}
