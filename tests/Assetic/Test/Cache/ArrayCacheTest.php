<?php namespace Assetic\Test\Cache;

use PHPUnit\Framework\TestCase;
use Assetic\Cache\ArrayCache;

/**
 * @group integration
 */
class ArrayCacheTest extends TestCase
{
    public function testCache()
    {
        $cache = new ArrayCache();

        $this->assertFalse($cache->has('foo'));

        $cache->set('foo', 'bar');
        $this->assertEquals('bar', $cache->get('foo'));

        $this->assertTrue($cache->has('foo'));

        $cache->remove('foo');
        $this->assertFalse($cache->has('foo'));
    }
}
