<?php

namespace Assetic\Test\Filter\Yui;

use Assetic\Filter\Yui\YuiCompressorCssFilter;

class YuiCompressorCssFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new YuiCompressorCssFilter('/path/to/jar');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'YuiCompressorCssFilter implements FilterInterface');
    }
}
