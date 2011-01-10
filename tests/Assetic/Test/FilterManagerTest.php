<?php

namespace Assetic\Test;

use Assetic\FilterManager;

class FilterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidName()
    {
        $this->setExpectedException('InvalidArgumentException');

        $fm = new FilterManager();
        $fm->get('foo');
    }

    public function testGetFilter()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $name = 'foo';

        $fm = new FilterManager();
        $fm->set($name, $filter);

        $this->assertSame($filter, $fm->get($name), '->set() sets a filter');
    }
}
