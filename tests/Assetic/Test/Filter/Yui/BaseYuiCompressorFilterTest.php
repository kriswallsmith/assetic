<?php

namespace Assetic\Test\Filter\Yui;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\Yui\BaseYuiCompressorFilter;

class BaseYuiCompressorFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new YuiCompressorFilterForTest('/path/to/jar');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'BaseYuiCompressorFilter implements FilterInterface');
    }
}

class YuiCompressorFilterForTest extends BaseYuiCompressorFilter
{
    public function filterDump(AssetInterface $asset)
    {
    }
}
