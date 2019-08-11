<?php namespace Assetic\Test\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Filter\FilterCollection;

class FilterCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new FilterCollection();
        $this->assertInstanceOf(FilterInterface::class, $filter, 'FilterCollection implements FilterInterface');
    }

    public function testEnsure()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();

        $filter->expects($this->once())->method('filterLoad');

        $coll = new FilterCollection();
        $coll->ensure($filter);
        $coll->ensure($filter);
        $coll->filterLoad($asset);
    }

    public function testAll()
    {
        $filter = new FilterCollection(array(
            $this->getMockBuilder(FilterInterface::class)->getMock(),
            $this->getMockBuilder(FilterInterface::class)->getMock(),
        ));

        $this->assertInternalType('array', $filter->all(), '->all() returns an array');
    }

    public function testEmptyAll()
    {
        $filter = new FilterCollection();
        $this->assertInternalType('array', $filter->all(), '->all() returns an array');
    }

    public function testCountable()
    {
        $filters = new FilterCollection(array($this->getMockBuilder(FilterInterface::class)->getMock()));

        $this->assertEquals(1, count($filters), 'Countable returns the count');
    }
}
