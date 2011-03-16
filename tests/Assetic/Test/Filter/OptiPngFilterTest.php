<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\OptiPngFilter;

class OptiPngFilterTest extends \PHPUnit_Framework_TestCase
{
    private $filter;

    protected function setUp()
    {
        if (!isset($_SERVER['OPTIPNG_BIN'])) {
            $this->markTestSkipped('No OptiPNG configuration.');
        }

        $this->filter = new OptiPngFilter($_SERVER['OPTIPNG_BIN']);
    }

    public function testFilter()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/home.png');
        $asset->load();

        $this->filter->filterDump($asset);
    }
}
