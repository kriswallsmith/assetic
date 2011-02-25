<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Factory;

use Assetic\Factory\LazyAssetManager;

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

        $this->am->addLoader('foo', $loader);
        $this->am->addResource('foo', $resource);

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

        $this->am->addResource('foo', $resources[0]);
        $this->am->addResource('bar', $resources[1]);

        $ret = $this->am->getResources();

        foreach ($resources as $resource) {
            $this->assertTrue(in_array($resource, $ret, true));
        }
    }
}
