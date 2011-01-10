<?php

namespace Assetic\Test\Filter\Sass;

use Assetic\Filter\Sass\SassFilter;

class SassFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new SassFilter('/path/to/sass');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'SassFilter implements FilterInterface');
    }
}
