<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\StringAsset;

class StringAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $asset = new StringAsset('');
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $asset, 'Asset implements AssetInterface');
    }

    public function testLoadAppliesFilters()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filterLoad');

        $asset = new StringAsset('foo', array($filter));
        $asset->load();
    }

    public function testAutomaticLoad()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filterLoad');

        $asset = new StringAsset('foo', array($filter));
        $asset->dump();
    }

    public function testGetFilters()
    {
        $asset = new StringAsset('');
        $this->assertInternalType('array', $asset->getFilters(), '->getFilters() returns an array');
    }

    public function testLoadAppliesAdditionalFilter()
    {
        $asset = new StringAsset('');
        $asset->load();

        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())
            ->method('filterLoad')
            ->with($asset);

        $asset->load($filter);
    }

    public function testDumpAppliesAdditionalFilter()
    {
        $asset = new StringAsset('');

        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())
            ->method('filterDump')
            ->with($asset);

        $asset->dump($filter);
    }

    public function testLastModified()
    {
        $asset = new StringAsset('');
        $asset->setLastModified(123);
        $this->assertEquals(123, $asset->getLastModified(), '->getLastModified() return the set last modified value');
    }

    public function testGetContentNullUnlessLoaded()
    {
        // see https://github.com/kriswallsmith/assetic/pull/432
        $asset = new StringAsset("test");
        $this->assertNull($asset->getContent(), '->getContent() returns null unless load() has been called.');

        $asset->load();

        $this->assertEquals("test", $asset->getContent(), '->getContent() returns the content after load()');
    }
}
