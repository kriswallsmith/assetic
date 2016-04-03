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
        if (!class_exists('Leafo\ScssPhp\Compiler')) {
            $this->markTestSkipped('leafo/scssphp is not installed');
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
        if (!class_exists('scss_compass')) {
            $this->markTestSkipped('leafo/scssphp-compass is not installed');
        }

        $expected = <<<EOF
.shadow {
  -webkit-box-shadow: 10px 10px 8px red;
  -moz-box-shadow: 10px 10px 8px red;
  box-shadow: 10px 10px 8px red; }

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
        $this->setExpectedExceptionRegExp(
            'Exception',
            '/^Undefined mixin box-shadow:.*line:* 4$/'
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

        $this->assertContains('color: red', $asset->getContent(), 'Import paths are correctly used');
    }

    public function testRegisterFunction()
    {
        $asset = new StringAsset('.foo{ color: bar(); }');
        $asset->load();

        $filter = $this->getFilter();
        $filter->registerFunction('bar',function () { return 'red';});
        $filter->filterLoad($asset);

        $this->assertContains('color: red', $asset->getContent(), 'custom function can be registered');
    }

    public function testSetFormatter()
    {
        $actual = new StringAsset(".foo {\n  color: #fff;\n}");
        $actual->load();

        $filter = $this->getFilter();
        $filter->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
        $filter->filterLoad($actual);

        $this->assertRegExp(
            '/^\.foo{color:#fff;?}$/',
            $actual->getContent(),
            'scss_formatter can be changed'
        );
    }

    /**
     * @group legacy
     */
    public function testSetFormatterWithLegacyName()
    {
        $actual = new StringAsset(".foo {\n  color: #fff;\n}");
        $actual->load();

        $filter = $this->getFilter();
        $filter->setFormatter('scss_formatter_compressed');
        $filter->filterLoad($actual);

        $this->assertRegExp(
            '/^\.foo{color:#fff;?}$/',
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

    public function testGetChildrenEmptyPath()
    {
        $factory = new AssetFactory(__DIR__.'/fixtures/sass');

        $filter = $this->getFilter();
        $filter->addImportPath(__DIR__.'/fixtures/sass');

        $children = $filter->getChildren($factory, '@import "main";');

        $this->assertCount(2, $children);
    }

    public function testSetVariables()
    {
        $filter = $this->getFilter();
        $filter->setVariables(array('color' => 'red'));

        $asset = new StringAsset("#test { color: \$color; }");
        $asset->load();
        $filter->filterLoad($asset);

        $this->assertContains('color: red', $asset->getContent(), 'Variables can be added');
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
