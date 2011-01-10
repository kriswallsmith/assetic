<?php

namespace Assetic\Test\Filter;

use Assetic\Filter\CssRewriteFilter;

class CssRewriteFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new CssRewriteFilter();
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'CssRewriteFilter implements FilterInterface');
    }
}
