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
use Assetic\Filter\CssoFilter;

class CssoFilterTest extends FilterTestCase
{
    /**
     * @var string
     */
    private $cssoPath;

    protected function setUp()
    {
        $this->cssoPath = $this->findExecutable('csso', 'CSSO_BIN');
        if (!$this->cssoPath) {
            $this->markTestSkipped('csso is not installed.');
        }
    }

    public function testRelativeSourceUrlImportImports()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/csso/main.css');
        $asset->load();

        $filter = new CssoFilter($this->cssoPath);
        $filter->filterDump($asset);
        $this->assertEquals('body{background:#000}div.foo{padding:2px}', $asset->getContent());
    }
}
