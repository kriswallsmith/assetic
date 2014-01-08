<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
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
class UglifyCssFilterTest extends FilterTestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        $uglifycssBin = $this->findExecutable('uglifycss', 'UGLIFYCSS_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');
        if (!$uglifycssBin) {
            $this->markTestSkipped('Unable to find `uglifycss` executable.');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/uglifycss/main.css');
        $this->asset->load();

        $this->filter = new UglifyCssFilter($uglifycssBin, $nodeBin);
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
