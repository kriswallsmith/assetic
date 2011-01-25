<?php

namespace Assetic\Test;

use Assetic\AssetFactory;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $asset = $this->factory->createAsset(array('@jquery'), array(), false);
        $this->assertInstanceOf('Assetic\\Asset\\AssetReference', $asset, '->createAsset() creates a reference');
    }

    public function testCreateFileAsset()
    {
        $asset = $this->factory->createAsset(array(basename(__FILE__)));
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset, '->createAsset() creates a file asset');
    }

    public function testCreateGlobAsset()
    {
        $asset = $this->factory->createAsset(array('*'));
        $this->assertInstanceOf('Assetic\\Asset\\GlobAsset', $asset, '->createAsset() creates a glob asset');
    }

    public function testCreateAssetCollection()
    {
        $asset = $this->factory->createAsset(array('*', basename(__FILE__)));
        $this->assertInstanceOf('Assetic\\Asset\\AssetCollection', $asset, '->createAsset() creates an asset collection');
    }

    public function testUrl()
    {
        $asset = $this->factory->createAsset(array(), array(), 'js/foo.js');
        $this->assertEquals('js/foo.js', $asset->getUrl(), '->createAsset() assigns an URL');
    }

    /**
     * @dataProvider provideAssetUrls
     */
    public function testGeneratedUrl($assetUrls, $expectedExtension)
    {
        $asset = $this->factory->createAsset($assetUrls);
        $this->assertEquals($expectedExtension, pathinfo($asset->getUrl(), PATHINFO_EXTENSION), '->createAsset() uses the most common extension when generating an URL');
    }

    public function provideAssetUrls()
    {
        return array(
            array(array('foo.js'), 'js'),
            array(array('foo.js', 'foo.css', 'bar.css'), 'css'),
        );
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
}
