<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\StringAsset;

class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $asset = new StringAsset('');
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $asset, 'Asset implements AssetInterface');
    }

    public function testLoadAppliesFilters()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filterLoad');

        $asset = new StringAsset('foo', null, array($filter));
        $asset->load();
    }

    public function testAutomaticLoad()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filterLoad');

        $asset = new StringAsset('foo', null, array($filter));
        $asset->dump();
    }
}
