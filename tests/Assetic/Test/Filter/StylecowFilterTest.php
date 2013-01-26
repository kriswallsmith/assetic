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

use Assetic\Filter\StylecowFilter;
use Assetic\Asset\FileAsset;

/**
 * @group integration
 * @author Luke Mills <luke@lukemills.net>
 */
class StylecowFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Stylecow\Parser')) {
            $this->markTestSkipped('Stylecow is not installed.');
        }
    }

    public function testVendorPrefixPlugin()
    {
        $expectation = file_get_contents(__DIR__ . '/expectations/stylecow/vendor-prefixes.css');
        $asset = new FileAsset(__DIR__ . '/fixtures/stylecow/vendor-prefixes.css');
        $asset->load();

        $filter = new StylecowFilter();
        $filter->setFilter('VendorPrefixes');
        $filter->filterLoad($asset);

        $content = $asset->getContent();

        $this->assertEquals($expectation, $content);
    }

}
