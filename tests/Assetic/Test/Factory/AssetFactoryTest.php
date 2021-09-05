<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Factory;

use Assetic\Asset\AssetCollection;
use Assetic\Factory\AssetFactory;
use PHPUnit\Framework\TestCase;

class AssetFactoryTest extends TestCase
{
    private $am;
    private $fm;
    private $factory;

    protected function setUp()
    {
        $this->am = $this->getMockBuilder('Assetic\\AssetManager')->getMock();
        $this->fm = $this->getMockBuilder('Assetic\\FilterManager')->getMock();

        $this->factory = new AssetFactory(__DIR__);
        $this->factory->setAssetManager($this->am);
        $this->factory->setFilterManager($this->fm);
    }

    protected function tearDown()
    {
        $this->am = null;
        $this->fm = null;
        $this->factory = null;
    }

    public function testNoAssetManagerReference()
    {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('There is no asset manager.');

        $factory = new AssetFactory('.');
        $factory->createAsset(array('@foo'));
    }

    public function testNoAssetManagerNotReference()
    {
        $factory = new AssetFactory('.');
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $factory->createAsset(array('foo')));
    }

    public function testNoFilterManager()
    {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('There is no filter manager.');


        $factory = new AssetFactory('.');
        $factory->createAsset(array('foo'), array('foo'));
    }

    public function testCreateAssetReference()
    {
        $referenced = $this->getMockBuilder('Assetic\\Asset\\AssetInterface')->getMock();

        $this->am->expects($this->any())
            ->method('get')
            ->with('jquery')
            ->will($this->returnValue($referenced));

        $assets = $this->factory->createAsset(array('@jquery'));
        $arr = iterator_to_array($assets);
        $this->assertInstanceOf('Assetic\\Asset\\AssetReference', $arr[0], '->createAsset() creates a reference');
    }

    /**
     * @dataProvider getHttpUrls
     */
    public function testCreateHttpAsset($sourceUrl)
    {
        $assets = $this->factory->createAsset(array($sourceUrl));
        $arr = iterator_to_array($assets);
        $this->assertInstanceOf('Assetic\\Asset\\HttpAsset', $arr[0], '->createAsset() creates an HTTP asset');
    }

    public function getHttpUrls()
    {
        return array(
            array('http://example.com/foo.css'),
            array('https://example.com/foo.css'),
            array('//example.com/foo.css'),
        );
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
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $arr[0], '->createAsset() uses a glob to create a file assets');
    }

    public function testCreateGlobAssetAndLoadFiles()
    {
        $assets = $this->factory->createAsset(array('*/Fixtures/*/*'));
        $assets->load();

        $this->assertEquals(5, count(iterator_to_array($assets)), '->createAsset() adds files');
    }

    public function testCreateGlobAssetAndExcludeDirectories()
    {
        $assets = $this->factory->createAsset(array('*/Fixtures/*', '*/Fixtures/*/*'));
        $assets->load();

        $this->assertEquals(5, count(iterator_to_array($assets)), '->createAsset() excludes directories and add files');
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
            ->will($this->returnValue($this->getMockBuilder('Assetic\\Filter\\FilterInterface')->getMock()));

        $asset = $this->factory->createAsset(array(), array('foo'));
        $this->assertEquals(1, count($asset->getFilters()), '->createAsset() adds filters');
    }

    public function testInvalidFilter()
    {
        $this->expectException('InvalidArgumentException');

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
            ->will($this->returnValue($this->getMockBuilder('Assetic\\Filter\\FilterInterface')->getMock()));

        $this->factory->createAsset(array('foo.css'), array('?foo'));
    }

    public function testWorkers()
    {
        $worker = $this->getMockBuilder('Assetic\\Factory\\Worker\\WorkerInterface')->getMock();

        // called once on the collection and once on each leaf
        $worker->expects($this->exactly(3))
            ->method('process')
            ->with($this->isInstanceOf('Assetic\\Asset\\AssetInterface'));

        $this->factory->addWorker($worker);
        $this->factory->createAsset(array('foo.js', 'bar.js'));
    }

    public function testWorkerReturn()
    {
        $worker = $this->getMockBuilder('Assetic\\Factory\\Worker\\WorkerInterface')->getMock();
        $asset = $this->getMockBuilder('Assetic\\Asset\\AssetInterface')->getMock();

        $worker->expects($this->at(2))
            ->method('process')
            ->with($this->isInstanceOf('Assetic\\Asset\\AssetCollectionInterface'))
            ->will($this->returnValue($asset));

        $this->factory->addWorker($worker);
        $coll = $this->factory->createAsset(array('foo.js', 'bar.js'));

        $this->assertEquals(1, count(iterator_to_array($coll)));
    }

    public function testNestedFormula()
    {
        $this->fm->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->getMockBuilder('Assetic\\Filter\\FilterInterface')->getMock()));

        $inputs = array(
            'css/main.css',
            array(
                // nested formula
                array('css/more.sass'),
                array('foo'),
            ),
        );

        $asset = $this->factory->createAsset($inputs, array(), array('output' => 'css/*.css'));

        $i = 0;
        foreach ($asset as $leaf) {
            $i++;
        }

        $this->assertEquals(2, $i);
    }

    public function testGetLastModified()
    {
        $asset = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();
        $child = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();
        $filter1 = $this->getMockBuilder('Assetic\Filter\FilterInterface')->getMock();
        $filter2 = $this->getMockBuilder('Assetic\Filter\DependencyExtractorInterface')->getMock();

        $asset->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(123));
        $asset->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue(array($filter1, $filter2)));
        $asset->expects($this->once())
            ->method('ensureFilter')
            ->with($filter1);
        $filter2->expects($this->once())
            ->method('getChildren')
            ->with($this->factory)
            ->will($this->returnValue(array($child)));
        $child->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(456));
        $child->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue(array()));

        $this->assertEquals(456, $this->factory->getLastModified($asset));
    }

    public function testGetLastModifiedCollection()
    {
        $leaf = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();
        $child = $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock();
        $filter1 = $this->getMockBuilder('Assetic\Filter\FilterInterface')->getMock();
        $filter2 = $this->getMockBuilder('Assetic\Filter\DependencyExtractorInterface')->getMock();

        $asset = new AssetCollection();
        $asset->add($leaf);

        $leaf->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(123));
        $leaf->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue(array($filter1, $filter2)));
        $leaf->expects($this->once())
            ->method('ensureFilter')
            ->with($filter1);
        $filter2->expects($this->once())
            ->method('getChildren')
            ->with($this->factory)
            ->will($this->returnValue(array($child)));
        $child->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(456));
        $child->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue(array()));

        $this->assertEquals(456, $this->factory->getLastModified($asset));
    }
}
