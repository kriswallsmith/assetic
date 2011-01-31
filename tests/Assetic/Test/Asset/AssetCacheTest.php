<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\AssetCache;

class AssetCacheTest extends \PHPUnit_Framework_TestCase
{
    private $inner;
    private $cache;
    private $asset;

    protected function setUp()
    {
        $this->inner = $this->getMock('Assetic\\Asset\\AssetInterface');
        $this->cache = $this->getMock('Assetic\\Cache\\CacheInterface');

        $this->asset = new AssetCache($this->inner, $this->cache);
    }

    public function testLoadFromCache()
    {
        $content = 'asdf';

        $this->inner->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));
        $this->cache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(true));
        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->isType('string'))
            ->will($this->returnValue($content));
        $this->inner->expects($this->once())
            ->method('setContent')
            ->with($content);

        $this->asset->load();
    }

    public function testLoadToCache()
    {
        $content = 'asdf';

        $this->inner->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));
        $this->cache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(false));
        $this->inner->expects($this->once())->method('load');
        $this->inner->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($content));
        $this->cache->expects($this->once())
            ->method('set')
            ->with($this->isType('string'), $content);

        $this->asset->load();
    }

    public function testDumpFromCache()
    {
        $content = 'asdf';

        $this->inner->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));
        $this->cache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(true));
        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->isType('string'))
            ->will($this->returnValue($content));

        $this->assertEquals($content, $this->asset->dump(), '->dump() returns the cached value');
    }

    public function testDumpToCache()
    {
        $content = 'asdf';

        $this->inner->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));
        $this->cache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(false));
        $this->inner->expects($this->once())
            ->method('dump')
            ->will($this->returnValue($content));
        $this->cache->expects($this->once())
            ->method('set')
            ->with($this->isType('string'), $content);

        $this->assertEquals($content, $this->asset->dump(), '->dump() returns the dumped value');
    }
}
