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
use Assetic\Filter\CSSqueezeFilter;

/**
 * @group integration
 */
class CSSqueezeFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('CSSqueeze')) {
            $this->markTestSkipped('CSSqueeze is not installed.');
        }
    }

    public function testRelativeSourceUrlImportImports()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/cssmin/main.css');
        $asset->load();

        $filter = new CSSqueezeFilter();
        $filter->setConfiguration(array('BasePath' => __DIR__.'/fixtures/cssmin/')); 
        $filter->filterDump($asset);

        $this->assertEquals("body{background:#000;color:#fff}", $asset->getContent());
    }
}
