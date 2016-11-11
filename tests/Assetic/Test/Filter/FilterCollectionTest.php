<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Filter\FilterCollection;

class FilterCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new FilterCollection();
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'FilterCollection implements FilterInterface');
    }

    public function testEnsure()
    {
        $filter = $this->getMockBuilder('Assetic\\Filter\\FilterInterface')->getMock();
        $asset = $this->getMockBuilder('Assetic\\Asset\\AssetInterface')->getMock();

        $filter->expects($this->once())->method('filterLoad');

        $coll = new FilterCollection();
        $coll->ensure($filter);
        $coll->ensure($filter);
        $coll->filterLoad($asset);
    }

    public function testAll()
    {
        $filter = new FilterCollection(array(
            $this->getMockBuilder('Assetic\\Filter\\FilterInterface')->getMock(),
            $this->getMockBuilder('Assetic\\Filter\\FilterInterface')->getMock(),
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
        $filters = new FilterCollection(array($this->getMockBuilder('Assetic\\Filter\\FilterInterface')->getMock()));

        $this->assertEquals(1, count($filters), 'Countable returns the count');
    }
}
