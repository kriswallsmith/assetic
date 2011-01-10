<?php

namespace Assetic\Test\Filter\Yui;

use Assetic\Filter\Yui\YuiCompressorJsFilter;

class YuiCompressorJsFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new YuiCompressorJsFilter('/path/to/jar');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'YuiCompressorJsFilter implements FilterInterface');
    }
}
