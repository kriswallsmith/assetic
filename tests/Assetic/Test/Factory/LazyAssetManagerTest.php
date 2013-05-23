<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Factory;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\StringAsset;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\CallablesFilter;

class LazyAssetManagerTest extends \PHPUnit_Framework_TestCase
{
    private $factory;

    protected function setUp()
    {
        $this->factory = $this->getMockBuilder('Assetic\\Factory\\AssetFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->am = new LazyAssetManager($this->factory);
    }

    public function testGetFromLoader()
    {
        $resource = $this->getMock('Assetic\\Factory\\Resource\\ResourceInterface');
        $loader = $this->getMock('Assetic\\Factory\\Loader\\FormulaLoaderInterface');
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $formula = array(
            array('js/core.js', 'js/more.js'),
            array('?yui_js'),
            array('output' => 'js/all.js')
        );

        $loader->expects($this->once())
            ->method('load')
            ->with($resource)
            ->will($this->returnValue(array('foo' => $formula)));
        $this->factory->expects($this->once())
            ->method('createAsset')
            ->with($formula[0], $formula[1], $formula[2] + array('name' => 'foo'))
            ->will($this->returnValue($asset));

        $this->am->setLoader('foo', $loader);
        $this->am->addResource($resource, 'foo');

        $this->assertSame($asset, $this->am->get('foo'), '->get() returns an asset from the loader');

        // test the "once" expectations
        $this->am->get('foo');
    }

    public function testGetResources()
    {
        $resources = array(
            $this->getMock('Assetic\\Factory\\Resource\\ResourceInterface'),
            $this->getMock('Assetic\\Factory\\Resource\\ResourceInterface'),
        );

        $this->am->addResource($resources[0], 'foo');
        $this->am->addResource($resources[1], 'bar');

        $ret = $this->am->getResources();

        foreach ($resources as $resource) {
            $this->assertTrue(in_array($resource, $ret, true));
        }
    }

    public function testGetResourcesEmpty()
    {
        $this->am->getResources();
    }

    public function testSetFormula()
    {
        $this->am->setFormula('foo', array());
        $this->am->load();
        $this->assertTrue($this->am->hasFormula('foo'), '->load() does not remove manually added formulae');
    }

    public function testIsDebug()
    {
        $this->factory->expects($this->once())
            ->method('isDebug')
            ->will($this->returnValue(false));

        $this->assertSame(false, $this->am->isDebug(), '->isDebug() proxies the factory');
    }

    public function testGetLastModified()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');
        $child = $this->getMock('Assetic\Asset\AssetInterface');
        $filter1 = $this->getMock('Assetic\Filter\FilterInterface');
        $filter2 = $this->getMock('Assetic\Filter\DependencyExtractorInterface');

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

        $this->assertEquals(456, $this->am->getLastModified($asset));
    }

    public function testGetLastModifiedWithAssetCollection()
    {
        $asset = new StringAsset("unprocessed");
        $asset->setLastModified(123);

        $self = $this;

        $filter = new CallablesFilter(
            function (AssetInterface $asset)
            {
                $asset->setContent(str_replace("unprocessed", "processed", $asset->getContent()));
            },

            null,

            function (AssetFactory $factory, $content, $loadPath = null) use ($self)
            {
                $self->assertEquals('unprocessed', $content, "Dependency extraction happens on the content before the filter itself is applied");

                $dependedOnAsset = new StringAsset("depended-on asset");
                $dependedOnAsset->setLastModified(456);

                return array($dependedOnAsset);
            }
        );

        $filter2 = new CallablesFilter(
            function (AssetInterface $asset)
            {
                $asset->setContent(str_replace("processed", "even more processed", $asset->getContent()));
            }
        );


        $assetCollection = new AssetCollection(array($asset), array($filter, $filter2));

        $this->assertEquals(123, $asset->getLastModified());

        $this->assertEquals("even more processed", $assetCollection->dump());
        $this->assertEquals(123, $assetCollection->getLastModified());

        /*
         * Might be confusing that the LazyAssetManager's getLastModified() method
         * applies the "deep mtime" logic, whereas every other Asset returns
         * the "shallow" value from a method with the same name.
         */
        $this->assertEquals(456, $this->am->getLastModified($assetCollection));
    }
}
