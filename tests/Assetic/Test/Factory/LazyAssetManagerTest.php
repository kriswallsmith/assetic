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
    private $am;

    protected function setUp()
    {
        $this->factory = $this->getMockBuilder('Assetic\\Factory\\AssetFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->am = new LazyAssetManager($this->factory);
    }

    public function testFormula()
    {
        $formula = array(
            $sourceUrls  = array('@jquery', 'js/jquery.plugin.js'),
            $filterNames = array('?yui_css'),
            $targetUrl   = 'js/packed.js',
        );

        $expected = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->factory->expects($this->once())
            ->method('createAsset')
            ->with($sourceUrls, $filterNames, $targetUrl, 'core')
            ->will($this->returnValue($expected));

        $this->am->addFormulae(array('core' => $formula));
        $asset = $this->am->get('core');

        $this->assertSame($expected, $asset);

        // the factory is only called ->once()
        $this->am->get('core');
    }

    public function testAll()
    {
        $formula = array(
            $sourceUrls  = array('@jquery', 'js/jquery.plugin.js'),
            $filterNames = array('?yui_css'),
            $targetUrl   = 'js/packed.js',
        );

        $expected = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->factory->expects($this->once())
            ->method('createAsset')
            ->with($sourceUrls, $filterNames, $targetUrl, 'core')
            ->will($this->returnValue($expected));

        $this->am->addFormulae(array('core' => $formula));

        $all = $this->am->all();
        $this->assertArrayHasKey('core', $all);
        $this->assertSame($expected, $all['core']);
    }

    public function testHas()
    {
        $formula = array(
            array('@jquery', 'js/jquery.plugin.js'),
            array('?yui_css'),
            'js/packed.js',
        );

        $this->am->addFormulae(array('core' => $formula));

        $this->assertTrue($this->am->has('core'));
    }

    public function testGetFormulae()
    {
        $formulae = array('core' => array(
            array('@jquery', 'js/jquery.plugin.js'),
            array('?yui_css'),
            'js/packed.js',
        ));

        $this->am->addFormulae($formulae);
        $this->assertEquals($formulae, $this->am->getFormulae());
    }

    public function testInvalidName()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->am->addFormulae(array('@foo' => $this->getMock('Assetic\\Asset\\AssetInterface')));
    }
}
