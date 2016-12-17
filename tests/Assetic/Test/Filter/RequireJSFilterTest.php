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
use Assetic\Asset\StringAsset;
use Assetic\Filter\RequireJsFilter;

/**
 * @group integration
 */
class RequireJSFilterTest extends \PHPUnit_Framework_TestCase
{
    private $filter;

    protected function setUp()
    {
        if (!isset($_SERVER['NODE_BIN']) || !isset($_SERVER['NODE_PATH'])) {
            $this->markTestSkipped('No node.js configuration.');
        }

        $this->filter = new RequireJsFilter($_SERVER['NODE_BIN'], array($_SERVER['NODE_PATH']));
    }

    public function testImport()
    {
        $expected = 'require(function(a){a.foo()}),define("main",function(){}),require.config({deps:["main"]}),define("config",function(){})';

        $asset = new FileAsset(__DIR__.'/fixtures/requirejs/config.js');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent());
    }
}
