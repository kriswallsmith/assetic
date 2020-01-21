<?php namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\LessphpFilter;

/**
 * @property LessphpFilter $filter
 */
class LessphpFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp(): void
    {
        if (!class_exists('lessc')) {
            $this->markTestSkipped('LessPHP is not installed');
        }

        $this->filter = new LessphpFilter();
    }

    protected function tearDown(): void
    {
        $this->filter = null;
    }

    /**
     * @group integration
     */
    public function testFilterLoad()
    {
        $asset = new StringAsset('.foo{.bar{width:1+1;}}');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals('.foo .bar{width:2}', $asset->getContent(), '->filterLoad() parses the content');
    }

    /**
     * @group integration
     */
    public function testImport()
    {
        $expected = '.foo{color:blue}.foo{color:red}';

        $asset = new FileAsset(__DIR__.'/fixtures/less/main.less');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() sets an include path based on source url');
    }

    /**
     * @group integration
     */
    public function testLoadPath()
    {
        $expected = '.foo{color:blue}.foo{color:red}';

        $this->filter->addLoadPath(__DIR__.'/fixtures/less');

        $asset = new StringAsset('@import "main";');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() adds load paths to include paths');
    }

    /**
     * @group integration
     */
    public function testPresets()
    {
        $asset = new StringAsset('.foo { color: @bar }');
        $asset->load();

        $this->filter->setPresets(array('bar' => 'green'));
        $this->filter->filterLoad($asset);

        $this->assertStringContainsString('#008000', $asset->getContent(), '->setPresets() to pass variables into lessphp filter');
    }

    /**
     * @group integration
     */
    public function testFormatterLessjs()
    {
        $asset = new StringAsset('.foo { color: green; }');
        $asset->load();

        $this->filter->setFormatter('lessjs');
        $this->filter->filterLoad($asset);

        $this->assertStringContainsString("color", $asset->getContent(), '->setFormatter("lessjs")');
    }

    /**
     * @group integration
     */
    public function testFormatterCompressed()
    {
        $asset = new StringAsset('.foo { color: green; }');
        $asset->load();

        $this->filter->setFormatter('compressed');
        $this->filter->filterLoad($asset);

        $this->assertStringContainsString('color:green', $asset->getContent(), '->setFormatter("compressed")');
    }

    /**
     * @group integration
     */
    public function testFormatterClassic()
    {
        $asset = new StringAsset('.foo { color: green; }');
        $asset->load();

        $this->filter->setFormatter('classic');
        $this->filter->filterLoad($asset);

        $this->assertStringContainsString('{color:green}', $asset->getContent(), '->setFormatter("classic")');
    }

    /**
     * @dataProvider provideImports
     */
    public function testGetChildren($import)
    {
        $children = $this->filter->getChildren(new AssetFactory('/'), $import, __DIR__.'/fixtures/less');

        $this->assertCount(1, $children);
        $this->assertEquals('main.less', $children[0]->getSourcePath());
    }

    public function provideImports()
    {
        return array(
            array('@import \'main.less\';'),
            array('@import "main.less";'),
            array('@import url(\'main.less\');'),
            array('@import url("main.less");'),
            array('@import url(main.less);'),
            array('@import \'main\';'),
            array('@import "main";'),
            array('@import url(\'main\');'),
            array('@import url("main");'),
            array('@import url(main);'),
            array('@import-once \'main.less\';'),
            array('@import-once "main.less";'),
            array('@import-once url(\'main.less\');'),
            array('@import-once url("main.less");'),
            array('@import-once url(main.less);'),
            array('@import-once \'main\';'),
            array('@import-once "main";'),
            array('@import-once url(\'main\');'),
            array('@import-once url("main");'),
            array('@import-once url(main);'),
        );
    }
}
