<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Resolver;

use Assetic\Resolver\AssetReferenceResolver;

class AssetReferenceResolverTest extends \PHPUnit_Framework_TestCase
{
    private $locator;

    protected function setUp()
    {
        $am = $this->getMockBuilder('Assetic\\AssetManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->locator = new AssetReferenceResolver($am);
    }

    public function testCorrectInput()
    {
        $asset = $this->locator->resolve('@jquery');

        $this->assertNotNull($asset);
        $this->assertInstanceOf('Assetic\\Asset\\AssetReference', $asset);
    }

    public function testWrongInput()
    {
        $asset = $this->locator->resolve('jquery');

        $this->assertNull($asset);
    }
}
