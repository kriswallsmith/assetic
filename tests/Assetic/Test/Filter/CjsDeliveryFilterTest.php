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
use Assetic\Asset\StringAsset;
use Assetic\Filter\CjsDeliveryFilter;

/**
 * @group integration
 */
class CjsDeliveryFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('\\MattCG\\cjsDelivery\\DeliveryFactory')) {
            $this->markTestSkipped('cjsDelivery is not installed');
        }
    }

    public function testSingleModuleCompile()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/commonjs/lib/novendordeps.js');
        $asset->load();

        $this->getFilter()->filterLoad($asset);

        $this->assertGreaterThan(0, strpos($asset->getContent(), 'Application module not depending on vendor module'), '->filterLoad() compiles a single module with no dependencies');
    }

    public function testMultiModuleCompile()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/commonjs/indexnovendordeps.js');
        $asset->load();

        $this->getFilter()->filterLoad($asset);

        $this->assertGreaterThan(0, strpos($asset->getContent(), 'Application module not depending on vendor module'), '->filterLoad() compiles a module with dependencies relative to path');
    }

    public function testMultiModuleCompileWithIncludePath()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/commonjs/lib/vendordeps.js');
        $asset->load();

        $filter = $this->getFilter();
        $filter->setIncludes(array(__DIR__.'/fixtures/commonjs/vendor'));
        $filter->filterLoad($asset);

        $this->assertGreaterThan(0, strpos($asset->getContent(), 'Vendor module'), '->filterLoad() compiles a module with dependencies in include path');
    }

    // private

    private function getFilter()
    {
        $filter = new CjsDeliveryFilter();

        return $filter;
    }
}
