<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Cache;

use Assetic\Cache\MemcachedCache;

/**
 * @group integration
 */
class MemcachedCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testCache()
    {
        $memcached = $this->getMock('Memcached');

        $memcached->expects($this->any())->method('get')->with('foo')->will($this->onConsecutiveCalls(
            false, // First call to MemcachedCache::has
            'bar', // First call to MemcachedCache::get
            'bar', // Second call to MemcachedCache::has
            false  // Third call to MemcachedCache::has
        ));

        $memcached->expects($this->once())->method('set')->with('foo', 'bar')->will($this->returnValue(true));
        $memcached->expects($this->once())->method('delete')->with('foo')->will($this->returnValue(true));

        $cache = new MemcachedCache($memcached);

        $this->assertFalse($cache->has('foo'));
        $this->assertTrue($cache->set('foo', 'bar'));
        $this->assertEquals('bar', $cache->get('foo'));

        $this->assertTrue($cache->has('foo'));

        $this->assertTrue($cache->remove('foo'));
        $this->assertFalse($cache->has('foo'));
    }

    public function testGetReturnsNullIfKeyDoesNotExist() {
        $memcached = $this->getMock('Memcached');

        $memcached->expects($this->once())->method('get')->with('key')->will($this->returnValue(false));

        $cache = new MemcachedCache($memcached);

        $this->assertNull($cache->get('key'));
    }
}
