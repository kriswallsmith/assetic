<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\BaseAsset;
use Assetic\Filter\FilterInterface;

class BaseAssetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldQueryFilterMtime()
    {
        $filter = $this->getMock('Assetic\\Filter\\LastModifiedFilterInterface');
        $filter->expects($this->once())
            ->method('getLastModified')
            ->will($this->returnValue(500));

        $asset = new BaseAssetForTest(array($filter));

        $this->assertEquals(500, $asset->getLastModified());
    }
}

class BaseAssetForTest extends BaseAsset
{
    public function load(FilterInterface $additionalFilter = null)
    {
    }

    public function getLastModified()
    {
        return $this->getLastModifiedPerFilters();
    }
}
