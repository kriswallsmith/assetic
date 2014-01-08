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
use Assetic\Filter\LessphpFilter;

/**
 * @property LessphpFilter $filter
 */
class LessphpFilterTest extends FilterTestCase
{
    protected $filter;

    protected function setUp()
    {
        if (!class_exists('lessc')) {
            $this->markTestSkipped('LessPHP is not installed');
        }

        $this->filter = new LessphpFilter();
    }

    /**
     * @group integration
     */
    public function testFilterLoad()
    {
        $asset = new StringAsset('.foo{.bar{width:1+1;}}');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals(".foo .bar {\n  width: 2;\n}\n", $asset->getContent(), '->filterLoad() parses the content');
    }

    /**
     * @group integration
     */
    public function testImport()
    {
        $expected = <<<EOF
.foo {
  color: blue;
}
.foo {
  color: red;
}

EOF;

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
        $expected = <<<EOF
.foo {
  color: blue;
}
.foo {
  color: red;
}

EOF;

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

        $this->assertContains('green', $asset->getContent(), '->setPresets() to pass variables into lessphp filter');
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

        $this->assertContains("\n  color", $asset->getContent(), '->setFormatter("lessjs")');
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

        $this->assertContains('color:green', $asset->getContent(), '->setFormatter("compressed")');
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

        $this->assertContains('{ color:green; }', $asset->getContent(), '->setFormatter("classic")');
    }

    /**
     * @group integration
     */
    public function testPreserveCommentsTrue()
    {
        $asset = new StringAsset("/* Line 1 */\n.foo { color: green }");
        $asset->load();

        $this->filter->setPreserveComments(true);
        $this->filter->filterLoad($asset);

        $this->assertContains('/* Line 1 */', $asset->getContent(), '->setPreserveComments(true)');
    }

    /**
     * @group integration
     */
    public function testPreserveCommentsFalse()
    {
        $asset = new StringAsset("/* Line 1 */\n.foo { color: green }");
        $asset->load();

        $this->filter->setPreserveComments(false);
        $this->filter->filterLoad($asset);

        $this->assertNotContains('/* Line 1 */', $asset->getContent(), '->setPreserveComments(false)');
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
