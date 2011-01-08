<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\Asset;

class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadAppliesFilters()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filterLoad');

        $asset = new Asset('foo', array($filter));
        $asset->load();
    }
}
