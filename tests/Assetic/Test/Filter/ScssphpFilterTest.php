<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\ScssphpFilter;

/**
 * @group integration
 */
class ScssphpFilterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('ScssPhp\ScssPhp\Compiler')) {
            $this->markTestSkipped('scssphp/scssphp is not installed');
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

    public function testSetImportPath()
    {
        $filter = $this->getFilter();
        $filter->addImportPath(__DIR__.'/fixtures/sass/import_path');

        $asset = new StringAsset("@import 'import';\n#test { color: \$red }");
        $asset->load();
        $filter->filterLoad($asset);

        $this->assertStringContainsString('color: red', $asset->getContent(), 'Import paths are correctly used');
    }

    public function testRegisterFunction()
    {
        $asset = new StringAsset('.foo{ color: bar(); }');
        $asset->load();

        $filter = $this->getFilter();
        $filter->registerFunction('bar',function () { return 'red';});
        $filter->filterLoad($asset);

        $this->assertStringContainsString('color: red', $asset->getContent(), 'custom function can be registered');
    }

    public function testSetFormatter()
    {
        $actual = new StringAsset(".foo {\n  color: #fff;\n}");
        $actual->load();

        $filter = $this->getFilter();
        $filter->setFormatter('ScssPhp\ScssPhp\Formatter\Compressed');
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

        $this->assertStringContainsString('color: red', $asset->getContent(), 'Variables can be added');
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
