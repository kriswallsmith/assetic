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
use Assetic\Filter\CssCrushFilter;

/**
 * @group integration
 */
class CssCrushFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('CssCrush', false)) {
            $this->markTestSkipped('CssCrush is not available.');
        }
    }

    public function testLoad()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/csscrush/main.css', array(), __DIR__.'/fixtures/csscrush', 'main.css');
        $asset->load();

        $filter = new CssCrushFilter();
        $filter->filterLoad($asset);

        $this->assertContains('strong { font-weight: bold; }', $asset->getContent(), '->filterLoad() calls CssCrush');
    }
}
