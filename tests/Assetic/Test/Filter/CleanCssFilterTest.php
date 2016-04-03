<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\CleanCssFilter;

/**
 * @group integration
 */
class CleanCssFilterTest extends FilterTestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        $cleancssBin = $this->findExecutable('cleancss', 'CLEANCSS_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');
        if (!$cleancssBin) {
            $this->markTestSkipped('Unable to find `cleancss` executable.');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/cleancss/main.css');
        $this->asset->load();

        $this->filter = new CleanCssFilter($cleancssBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testClean()
    {
        $this->filter->filterDump($this->asset);

        $expected = <<<CSS
@import url(fonts.css);/*! Copyright */body{background:#000}/*! Second special comment */a{color:#fff}
CSS;
        $this->assertSame($expected, $this->asset->getContent());
    }

    public function testRemoveSpecialComments()
    {
        $this->filter->setRemoveSpecialComments(true);
        $this->filter->filterDump($this->asset);

        $expected = <<<CSS
@import url(fonts.css);body{background:#000}a{color:#fff}
CSS;
        $this->assertSame($expected, $this->asset->getContent());
    }

    public function testKeepFirstSpecialComment()
    {
        $this->filter->setOnlyKeepFirstSpecialComment(true);
        $this->filter->filterDump($this->asset);

        $expected = <<<CSS
@import url(fonts.css);/*! Copyright */body{background:#000}a{color:#fff}
CSS;
        $this->assertSame($expected, $this->asset->getContent());
    }
}
