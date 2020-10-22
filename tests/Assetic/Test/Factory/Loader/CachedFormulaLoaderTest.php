<?php namespace Assetic\Test\Factory\Loader;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Factory\Loader\FormulaLoaderInterface;
use Assetic\Contracts\Factory\Resource\ResourceInterface;
use Assetic\Cache\ConfigCache;
use Assetic\Factory\Loader\CachedFormulaLoader;

class CachedFormulaLoaderTest extends TestCase
{
    protected $loader;
    protected $configCache;
    protected $resource;

    protected function setUp(): void
    {
        $this->loader = $this->getMockBuilder(FormulaLoaderInterface::class)->getMock();
        $this->configCache = $this->getMockBuilder(ConfigCache::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource = $this->getMockBuilder(ResourceInterface::class)->getMock();
    }

    protected function tearDown(): void
    {
        $this->loader = null;
        $this->configCache = null;
        $this->resource = null;
    }

    public function testNotDebug()
    {
        $expected = array(
            'foo' => array([], [], []),
            'bar' => array([], [], []),
        );

        $this->configCache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(false));
        $this->loader->expects($this->once())
            ->method('load')
            ->with($this->resource)
            ->will($this->returnValue($expected));
        $this->configCache->expects($this->once())
            ->method('set')
            ->with($this->isType('string'), $expected);

        $loader = new CachedFormulaLoader($this->loader, $this->configCache);
        $this->assertEquals($expected, $loader->load($this->resource), '->load() returns formulae');
    }

    public function testNotDebugCached()
    {
        $expected = array(
            'foo' => array([], [], []),
            'bar' => array([], [], []),
        );

        $this->configCache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(true));
        $this->resource->expects($this->never())
            ->method('isFresh');
        $this->configCache->expects($this->once())
            ->method('get')
            ->with($this->isType('string'))
            ->will($this->returnValue($expected));

        $loader = new CachedFormulaLoader($this->loader, $this->configCache);
        $this->assertEquals($expected, $loader->load($this->resource), '->load() returns formulae');
    }

    public function testDebugCached()
    {
        $timestamp = 123;
        $expected = array(
            'foo' => array([], [], []),
            'bar' => array([], [], []),
        );

        $this->configCache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(true));
        $this->configCache->expects($this->once())
            ->method('getTimestamp')
            ->with($this->isType('string'))
            ->will($this->returnValue($timestamp));
        $this->resource->expects($this->once())
            ->method('isFresh')
            ->with($timestamp)
            ->will($this->returnValue(true));
        $this->loader->expects($this->never())
            ->method('load');
        $this->configCache->expects($this->once())
            ->method('get')
            ->with($this->isType('string'))
            ->will($this->returnValue($expected));

        $loader = new CachedFormulaLoader($this->loader, $this->configCache, true);
        $this->assertEquals($expected, $loader->load($this->resource), '->load() returns formulae');
    }

    public function testDebugCachedStale()
    {
        $timestamp = 123;
        $expected = array(
            'foo' => array([], [], []),
            'bar' => array([], [], []),
        );

        $this->configCache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(true));
        $this->configCache->expects($this->once())
            ->method('getTimestamp')
            ->with($this->isType('string'))
            ->will($this->returnValue($timestamp));
        $this->resource->expects($this->once())
            ->method('isFresh')
            ->with($timestamp)
            ->will($this->returnValue(false));
        $this->loader->expects($this->once())
            ->method('load')
            ->with($this->resource)
            ->will($this->returnValue($expected));
        $this->configCache->expects($this->once())
            ->method('set')
            ->with($this->isType('string'), $expected);

        $loader = new CachedFormulaLoader($this->loader, $this->configCache, true);
        $this->assertEquals($expected, $loader->load($this->resource), '->load() returns formulae');
    }
}
