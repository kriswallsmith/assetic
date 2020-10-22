<?php namespace Assetic\Test\Cache;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Cache\CacheInterface;
use Assetic\Cache\ExpiringCache;

class ExpiringCacheTest extends TestCase
{
    private $inner;
    private $lifetime;
    private $cache;

    protected function setUp(): void
    {
        $this->inner = $this->getMockBuilder(CacheInterface::class)->getMock();
        $this->lifetime = 3600;
        $this->cache = new ExpiringCache($this->inner, $this->lifetime);
    }

    protected function tearDown(): void
    {
        $this->inner = null;
        $this->lifetime = null;
        $this->cache = null;
    }

    public function testHasExpired()
    {
        $key = 'asdf';
        $expiresKey = 'asdf.expires';
        $thePast = 0;

        $this->inner->expects($this->once())
            ->method('has')
            ->with($key)
            ->willReturn(true);
        $this->inner->expects($this->once())
            ->method('get')
            ->with($expiresKey)
            ->willReturn($thePast);
        $this->inner->expects($this->exactly(2))
            ->method('remove')
            ->withConsecutive(
                [$expiresKey],
                [$key]
            );

        $this->assertFalse($this->cache->has($key), '->has() returns false if an expired value exists');
    }

    public function testHasNotExpired()
    {
        $key = 'asdf';
        $expiresKey = 'asdf.expires';
        $theFuture = time() * 2;

        $this->inner->expects($this->once())
            ->method('has')
            ->with($key)
            ->will($this->returnValue(true));
        $this->inner->expects($this->once())
            ->method('get')
            ->with($expiresKey)
            ->will($this->returnValue($theFuture));

        $this->assertTrue($this->cache->has($key), '->has() returns true if a value the not expired');
    }

    public function testSetLifetime()
    {
        $key = 'asdf';
        $expiresKey = 'asdf.expires';
        $value = 'qwerty';

        $this->inner->expects($this->exactly(2))
            ->method('set')
            ->withConsecutive(
                [$expiresKey, $this->greaterThanOrEqual(time() + $this->lifetime)],
                [$key, $value]
            );

        $this->cache->set($key, $value);
    }

    public function testRemove()
    {
        $key = 'asdf';
        $expiresKey = 'asdf.expires';

        $this->inner->expects($this->exactly(2))
            ->method('remove')
            ->withConsecutive(
                [$expiresKey],
                [$key]
            );

        $this->cache->remove($key);
    }

    public function testGet()
    {
        $this->inner->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue('bar'));

        $this->assertEquals('bar', $this->cache->get('foo'), '->get() returns the cached value');
    }
}
