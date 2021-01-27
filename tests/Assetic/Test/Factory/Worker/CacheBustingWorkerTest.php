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
     * @dataProvider providePathExpectations
     */
    public function shouldApplyHash($target, $expectedStart, $expectedEnd)
    {
        $asset = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();
        $factory = $this->getMockBuilder('Assetic\Factory\AssetFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue($target));
        $factory->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->once())
            ->method('setTargetPath')
            ->with($this->logicalAnd(
                $this->stringStartsWith($expectedStart),
                $this->stringEndsWith($expectedEnd),
                $this->matchesRegularExpression('/^'.preg_quote($expectedStart, '/').'[a-z0-9]{7}'.preg_quote($expectedEnd, '/').'$/')
            ));

        $this->worker->process($asset, $factory);
    }

    public function providePathExpectations()
    {
        return array(
            array('main.js', 'main-', '.js'),
            array('css/main.css', 'css/main-', '.css'),
            array('css/file-nothash_.css', 'css/file-nothash_-', '.css'),

            // Strip parent hash
            array('main-32d5523_leaf.js', 'main_leaf-', '.js'),
            array('main-32d5523_love-my6char_file.js', 'main_love-my6char_file-', '.js'),
            array('css-7110eda_css/main-7110eda_leaf.css', 'css-7110eda_css/main_leaf-', '.css'),
        );
    }

    /**
     * @test
     */
    public function shouldApplyConsistentHash()
    {
        $asset = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();
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
            ->will($this->returnCallback(function ($path) use (&$paths) {
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
        $asset = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();
        $factory = $this->getMockBuilder('Assetic\Factory\AssetFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $path = null;

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnCallback(function () use (&$path) {
                return $path ?: 'css/main.css';
            }));
        $factory->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->once())
            ->method('setTargetPath')
            ->will($this->returnCallback(function ($arg) use (&$path) {
                $path = $arg;
            }));

        $this->worker->process($asset, $factory);
        $this->worker->process($asset, $factory);
    }
}
