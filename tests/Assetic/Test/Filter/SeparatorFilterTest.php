<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\StringAsset;
use Assetic\Filter\SeparatorFilter;

/**
 * @group integration
 */
class SeparatorFilterTest extends TestCase
{
    public function testAppend()
    {
        $asset = new StringAsset('foobar');
        $asset->load();

        $filter = new SeparatorFilter('+');
        $filter->filterDump($asset);

        $this->assertEquals('foobar+', $asset->getContent());
    }
}
