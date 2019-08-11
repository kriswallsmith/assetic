<?php namespace Assetic\Test\Filter;

use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Filter\BaseProcessFilter;
use Assetic\Contracts\Asset\AssetInterface;

class BaseProcessFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new BaseProcessFilterFilter();
        $this->assertInstanceOf(FilterInterface::class, $filter, 'BaseProcessFilter implements FilterInterface');
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
