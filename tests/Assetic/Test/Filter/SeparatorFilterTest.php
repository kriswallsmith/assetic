<?php namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\SeparatorFilter;

/**
 * @group integration
 */
class SeparatorFilterTest extends \PHPUnit_Framework_TestCase
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
