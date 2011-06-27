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
    public function shouldLoadAssetLazilyWhenQueryingMtime()
    {
        $test = $this;
        $callback = function($asset) use($test)
        {
            $test->assertEquals('asdf', $asset->getContent(), 'the asset is loaded');
            return 500;
        };

        $filter = $this->getMock('Assetic\\Filter\\LastModifiedFilterInterface');
        $filter->expects($this->once())
            ->method('getLastModified')
            ->with($this->isInstanceOf('Assetic\\Asset\\AssetInterface'))
            ->will($this->returnCallback($callback));

        $asset = new BaseAssetForTest(array($filter));
        $asset->content = 'asdf';
        $asset->getLastModified();
    }

    /**
     * @test
     */
    public function shouldRunFiltersLazilyWhenQueryingMtime()
    {
        $filter1 = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter2 = $this->getMock('Assetic\\Filter\\LastModifiedFilterInterface');
        $filter3 = $this->getMock('Assetic\\Filter\\FilterInterface');

        $filter1->expects($this->once())->method('filterLoad');
        $filter2->expects($this->never())->method('filterLoad');
        $filter3->expects($this->never())->method('filterLoad');

        $asset = new BaseAssetForTest(array($filter1, $filter2, $filter3));
        $asset->getLastModified();

        $this->assertEquals(3, count($asset->getFilters()), 'asset retains filters');
    }
}

class BaseAssetForTest extends BaseAsset
{
    public $content;

    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad($this->content, $additionalFilter);
    }

    public function getLastModified()
    {
        return $this->getLastModifiedPerFilters();
    }
}
