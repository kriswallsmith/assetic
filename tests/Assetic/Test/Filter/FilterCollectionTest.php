<?php namespace Assetic\Test\Filter;

use Assetic\Filter\FilterCollection;

class FilterCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new FilterCollection();
        $this->assertInstanceOf('Assetic\\Contracts\\Filter\\FilterInterface', $filter, 'FilterCollection implements FilterInterface');
    }

    public function testEnsure()
    {
        $filter = $this->getMockBuilder('Assetic\\Contracts\\Filter\\FilterInterface')->getMock();
        $asset = $this->getMockBuilder('Assetic\\Contracts\\Asset\\AssetInterface')->getMock();

        $filter->expects($this->once())->method('filterLoad');

        $coll = new FilterCollection();
        $coll->ensure($filter);
        $coll->ensure($filter);
        $coll->filterLoad($asset);
    }

    public function testAll()
    {
        $filter = new FilterCollection(array(
            $this->getMockBuilder('Assetic\\Contracts\\Filter\\FilterInterface')->getMock(),
            $this->getMockBuilder('Assetic\\Contracts\\Filter\\FilterInterface')->getMock(),
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
        $filters = new FilterCollection(array($this->getMockBuilder('Assetic\\Contracts\\Filter\\FilterInterface')->getMock()));

        $this->assertEquals(1, count($filters), 'Countable returns the count');
    }
}
