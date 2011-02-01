<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test;

use Assetic\AssetFactory;

class AssetFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $baseDir;
    private $am;
    private $fm;
    private $factory;

    protected function setUp()
    {
        $this->baseDir = __DIR__;
        $this->am = $this->getMock('Assetic\\AssetManager');
        $this->fm = $this->getMock('Assetic\\FilterManager');

        $this->factory = new AssetFactory($this->baseDir, $this->am, $this->fm);
    }

    public function testCreateAssetReference()
    {
        $assets = $this->factory->createAsset(array('@jquery'));
        $arr = iterator_to_array($assets);
        $this->assertInstanceOf('Assetic\\Asset\\AssetReference', $arr[0], '->createAsset() creates a reference');
    }

    public function testCreateFileAsset()
    {
        $assets = $this->factory->createAsset(array(basename(__FILE__)));
        $arr = iterator_to_array($assets);
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $arr[0], '->createAsset() creates a file asset');
    }

    public function testCreateGlobAsset()
    {
        $assets = $this->factory->createAsset(array('*'));
        $arr = iterator_to_array($assets);
        $this->assertInstanceOf('Assetic\\Asset\\GlobAsset', $arr[0], '->createAsset() creates a glob asset');
    }

    public function testCreateAssetCollection()
    {
        $asset = $this->factory->createAsset(array('*', basename(__FILE__)));
        $this->assertInstanceOf('Assetic\\Asset\\AssetCollection', $asset, '->createAsset() creates an asset collection');
    }

    public function testFilter()
    {
        $this->fm->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->getMock('Assetic\\Filter\\FilterInterface')));

        $asset = $this->factory->createAsset(array(), array('foo'));
        $this->assertEquals(1, count($asset->getFilters()), '->createAsset() adds filters');
    }

    public function testInvalidFilter()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->fm->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->throwException(new \InvalidArgumentException()));

        $asset = $this->factory->createAsset(array(), array('foo'));
    }

    public function testOptionalInvalidFilter()
    {
        $this->factory->setDebug(true);

        $asset = $this->factory->createAsset(array(), array('?foo'));

        $this->assertEquals(0, count($asset->getFilters()), '->createAsset() does not add an optional invalid filter');
    }

    public function testIncludingOptionalFilter()
    {
        $this->fm->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->getMock('Assetic\\Filter\\FilterInterface')));

        $this->factory->createAsset(array('foo.css'), array('?foo'));
    }
}
