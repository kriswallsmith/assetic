<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\UglifyCssFilter;

/**
 * @group integration
 */
class UglifyCssFilterTest extends \PHPUnit_Framework_TestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        if (!isset($_SERVER['UGLIFYCSS_BIN'])) {
            $this->markTestSkipped('There is no uglifyCss configuration.');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/uglifycss/main.css');
        $this->asset->load();

        if (isset($_SERVER['NODE_BIN'])) {
            $this->filter = new UglifyCssFilter($_SERVER['UGLIFYCSS_BIN'], $_SERVER['NODE_BIN']);
        } else {
            $this->filter = new UglifyCssFilter($_SERVER['UGLIFYCSS_BIN']);
        }
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testUglify()
    {
        $this->filter->filterDump($this->asset);

        $expected = <<<CSS
@import url("fonts.css");body{background:black}
CSS;
        $this->assertSame($expected, $this->asset->getContent());
    }

}
