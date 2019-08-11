<?php namespace Assetic\Test\Cache;

use Assetic\Cache\ConfigCache;
use Assetic\Test\TestCase;

class ConfigCacheTest extends TestCase
{
    private $dir;
    private $cache;

    protected function setUp()
    {
        $this->dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('assetic_config_cache');
        mkdir($this->dir);

        $this->cache = new ConfigCache($this->dir);
    }

    protected function tearDown()
    {
        self::removeDirectory($this->dir);
        $this->dir = null;
        $this->cache = null;
    }

    public function testCache()
    {
        $this->cache->set('foo', array(1, 2, 3));
        $this->assertEquals(array(1, 2, 3), $this->cache->get('foo'), '->get() returns the ->set() value');
    }

    public function testTimestamp()
    {
        $this->cache->set('bar', array(4, 5, 6));
        $this->assertInternalType('integer', $time = $this->cache->getTimestamp('bar'), '->getTimestamp() returns an integer');
        $this->assertNotEmpty($time, '->getTimestamp() returns a non-empty number');
    }

    public function testInvalidValue()
    {
        $this->setExpectedException(\RuntimeException::class);
        $this->cache->get('_invalid');
    }

    public function testInvalidTimestamp()
    {
        $this->setExpectedException(\RuntimeException::class);
        $this->cache->getTimestamp('_invalid');
    }

    public function testHas()
    {
        $this->cache->set('foo', 'bar');
        $this->assertTrue($this->cache->has('foo'));
        $this->assertFalse($this->cache->has('_invalid'));
    }
}
