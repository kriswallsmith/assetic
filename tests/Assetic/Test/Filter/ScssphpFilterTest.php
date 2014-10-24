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
use Assetic\Asset\StringAsset;
use Assetic\Factory\AssetFactory;
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

    public function testCompassExtensionCanBeEnabled()
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
        $this->assertEquals(
            $expected,
            $asset->getContent(),
            'compass plugin can be enabled'
        );
    }

    public function testCompassExtensionCanBeDisabled()
    {
        $this->setExpectedException(
            "Exception",
            "Undefined mixin box-shadow: failed at `@include box-shadow(10px "
            ."10px 8px red);` line: 4"
        );

        $asset = new FileAsset(__DIR__.'/fixtures/sass/main_compass.scss');
        $asset->load();

        $this->getFilter(false)->filterLoad($asset);
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

    public function testRegisterFunction()
    {
        $asset = new StringAsset('.foo{ color: bar(); }');
        $asset->load();

        $filter = $this->getFilter();
        $filter->registerFunction('bar',function () { return 'red';});
        $filter->filterLoad($asset);

        $expected = new StringAsset('.foo{ color: red;}');
        $expected->load();
        $filter->filterLoad($expected);

        $this->assertEquals($expected->getContent(), $asset->getContent(), 'custom function can be registered');
    }

    public function testSetFormatter()
    {
        $actual = new StringAsset(".foo {\n  color: #fff;\n}");
        $actual->load();

        $filter = $this->getFilter();
        $filter->setFormatter("scss_formatter_compressed");
        $filter->filterLoad($actual);

        $expected = new StringAsset('.foo{color:#fff;}');
        $expected->load();

        $this->assertEquals(
            $expected->getContent(),
            $actual->getContent(),
            'scss_formatter can be changed'
        );
    }

    public function testGetChildren()
    {
        $factory = new AssetFactory('');

        $filter = $this->getFilter();
        $children = $filter->getChildren($factory, '@import "main";', __DIR__.'/fixtures/sass');

        $this->assertCount(2, $children);
    }

    public function testSetVariables()
    {
        $filter = $this->getFilter();
        $filter->setVariables(array('color' => 'red'));

        $asset = new StringAsset("#test { color: \$color; }");
        $asset->load();
        $filter->filterLoad($asset);

        $this->assertEquals("#test {\n  color: red; }\n", $asset->getContent(), "Variables can be added");
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
