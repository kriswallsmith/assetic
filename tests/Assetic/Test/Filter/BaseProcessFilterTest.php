<?php namespace Assetic\Test\Filter;

use Assetic\Filter\BaseProcessFilter;
use Assetic\Asset\AssetInterface;

class BaseProcessFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new BaseProcessFilterFilter();
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'BaseProcessFilter implements FilterInterface');
    }
}

class BaseProcessFilterFilter extends BaseProcessFilter
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
