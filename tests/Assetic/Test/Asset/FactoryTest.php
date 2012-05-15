<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    private $factory;

    protected function setUp()
    {
        $this->factory = new Factory();
    }

    protected function tearDown()
    {
        unset($this->factory);
    }

    /**
     * @test
     */
    public function shouldCreateAsset()
    {
        $this->assertInstanceOf('Assetic\Asset\AssetInterface', $this->factory->createAsset());
    }

    /**
     * @test
     */
    public function shouldSetAttributes()
    {
        $asset = $this->factory->createAsset(array('foo' => 'bar'));
        $this->assertEquals('bar', $asset->getAttribute('foo'));
    }
}
