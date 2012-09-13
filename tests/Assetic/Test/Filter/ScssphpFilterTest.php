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

use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\ScssphpFilter;

/**
 * @group integration
 */
class ScssphpFilterTest extends \PHPUnit_Framework_TestCase
{

    private function getFilter($compass = false)
    {
        $filter = new ScssphpFilter();
        if ($compass) {
            $filter->enableCompass();
        }
        return $filter;
    }

    public function testFilterLoad()
    {
        $expected = <<<EOF
.foo .bar {
  width: 2; }

EOF;

        $asset = new StringAsset('.foo{.bar{width:1+ 1;}}');
        $asset->load();

        $this->getFilter()->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() parses the content');
    }

    public function testImport()
    {
        $expected = <<<EOF
.foo {
  color: blue; }

.foo {
  color: red; }

EOF;

        $asset = new FileAsset(__DIR__.'/fixtures/sass/main.scss');
        $asset->load();

        $this->getFilter()->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() sets an include path based on source url');
    }

    public function testCompassExtension()
    {
        $expected = <<<EOF
.shadow {
  -webkit-box-shadow : 10px 10px 8px red;
  -moz-box-shadow : 10px 10px 8px red;
  box-shadow : 10px 10px 8px red; }

EOF;

        $asset = new FileAsset(__DIR__.'/fixtures/sass/main_compass.scss');
        $asset->load();

        $this->getFilter(true)->filterLoad($asset);
        $this->assertEquals($expected, $asset->getContent(), 'compass plugin can be enabled');

        $asset = new FileAsset(__DIR__.'/fixtures/sass/main_compass.scss');
        $asset->load();

        $this->getFilter(false)->filterLoad($asset);
        $this->assertEquals("@import \"compass\";\n", $asset->getContent(), 'compass plugin can be disabled');
    }
}
