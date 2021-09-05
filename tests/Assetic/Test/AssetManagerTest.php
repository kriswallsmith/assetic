<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test;

use Assetic\AssetManager;
use PHPUnit\Framework\TestCase;

class AssetManagerTest extends TestCase
{
    /** @var AssetManager */
    private $am;

    protected function setUp()
    {
        $this->am = new AssetManager();
    }

    public function testGetAsset()
    {
        $asset = $this->getMockBuilder('Assetic\\Asset\\AssetInterface')->getMock();
        $this->am->set('foo', $asset);
        $this->assertSame($asset, $this->am->get('foo'), '->get() returns an asset');
    }

    public function testGetInvalidAsset()
    {
        $this->expectException('InvalidArgumentException');
        $this->am->get('foo');
    }

    public function testHas()
    {
        $asset = $this->getMockBuilder('Assetic\\Asset\\AssetInterface')->getMock();
        $this->am->set('foo', $asset);

        $this->assertTrue($this->am->has('foo'), '->has() returns true if the asset is set');
        $this->assertFalse($this->am->has('bar'), '->has() returns false if the asset is not set');
    }

    public function testInvalidName()
    {
        $this->expectException('InvalidArgumentException');

        $this->am->set('@foo', $this->getMockBuilder('Assetic\\Asset\\AssetInterface')->getMock());
    }

    public function testClear()
    {
        $this->am->set('foo', $this->getMockBuilder('Assetic\Asset\AssetInterface')->getMock());
        $this->am->clear();

        $this->assertFalse($this->am->has('foo'));
    }
}
