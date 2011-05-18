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
use Assetic\Filter\CssImportFilter;

class CssImportFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testImport()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/cssimport/main.css', array(), __DIR__.'/fixtures/cssimport');
        $asset->load();

        $filter = new CssImportFilter();
        $filter->filterLoad($asset);

        $expected = <<<EOF
body { color: red; }


body { color: black; }

EOF;

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() inlines CSS imports');
    }
}
