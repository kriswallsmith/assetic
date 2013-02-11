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
use Assetic\Filter\ScssphpFilter;

/**
 * @group integration
 */
class ScssphpFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('scssc')) {
            $this->markTestSkipped('scssphp is not installed');
        }
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
        $this->markTestIncomplete('Someone fix this, SVP? (Undefined mixin "box-shadow")');

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

    public function testSetImportPath()
    {
        $filter = $this->getFilter();
        $filter->addImportPath(__DIR__.'/fixtures/sass/import_path');

        $asset = new StringAsset("@import 'import';\n#test { color: \$red }");
        $asset->load();
        $filter->filterLoad($asset);

        $this->assertEquals("#test {\n  color: red; }\n", $asset->getContent(), 'Import paths are correctly used');
    }

    // private

    private function getFilter($compass = false)
    {
        $filter = new ScssphpFilter();

        if ($compass) {
            $filter->enableCompass();
        }

        return $filter;
    }
}
