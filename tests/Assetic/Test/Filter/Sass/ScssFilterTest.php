<?php

namespace Assetic\Test\Filter\Sass;

use Assetic\Filter\Sass\ScssFilter;

class ScssFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new ScssFilter('/path/to/sass');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'ScssFilter implements FilterInterface');
    }
}
