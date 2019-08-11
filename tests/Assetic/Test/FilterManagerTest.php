<?php namespace Assetic\Test;

use Assetic\Contracts\Filter\FilterInterface;
use Assetic\FilterManager;

class FilterManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilterManager */
    private $fm;

    protected function setUp()
    {
        $this->fm = new FilterManager();
    }

    public function testInvalidName()
    {
        $this->setExpectedException('InvalidArgumentException');

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
        $this->setExpectedException('InvalidArgumentException');
        $this->fm->set('@foo', $this->getMockBuilder(FilterInterface::class)->getMock());
    }
}
