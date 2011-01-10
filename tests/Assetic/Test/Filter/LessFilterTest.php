<?php

namespace Assetic\Test\Filter;

use Assetic\Filter\LessFilter;

class LessFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new LessFilter();
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'LessFilter implements FilterInterface');
    }
}
