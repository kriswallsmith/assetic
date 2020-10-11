<?php namespace Assetic\Test;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\FilterManager;

class FilterManagerTest extends TestCase
{
    /** @var FilterManager */
    private $fm;

    protected function setUp(): void
    {
        $this->fm = new FilterManager();
    }

    public function testInvalidName()
    {
        $this->expectException('InvalidArgumentException');

        $this->fm->get('foo');
    }

    public function testGetFilter()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $name = 'foo';

        $this->fm->set($name, $filter);

        $this->assertSame($filter, $this->fm->get($name), '->set() sets a filter');
    }

    public function testHas()
    {
        $this->fm->set('foo', $this->getMockBuilder(FilterInterface::class)->getMock());
        $this->assertTrue($this->fm->has('foo'), '->has() returns true if the filter is set');
    }

    public function testHasInvalid()
    {
        $this->assertFalse($this->fm->has('foo'), '->has() returns false if the filter is not set');
    }

    public function testInvalidAlias()
    {
        $this->expectException('InvalidArgumentException');
        $this->fm->set('@foo', $this->getMockBuilder(FilterInterface::class)->getMock());
    }
}
