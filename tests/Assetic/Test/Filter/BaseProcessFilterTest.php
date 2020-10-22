<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Filter\BaseProcessFilter;
use Assetic\Contracts\Asset\AssetInterface;

class BaseProcessFilterTest extends TestCase
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
