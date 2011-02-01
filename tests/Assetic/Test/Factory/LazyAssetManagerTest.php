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
}
