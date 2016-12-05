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
use Assetic\Factory\AssetFactory;
use Assetic\Filter\LessRecessFilter;
/**
 * @group integration
 */
class LessRecessFilterTest extends FilterTestCase
{
    /**
     * @var LessFilter
     */
    protected $filter;

    protected function setUp()
    {
        if (!$nodeBin = $this->findExecutable('node', 'NODE_BIN')) {
            $this->markTestSkipped('Unable to find `node` executable.');
        }

        if (!$this->checkNodeModule('recess', $nodeBin)) {
            $this->markTestSkipped('The "recess" module is not installed.');
        }

        $this->filter = new LessRecessFilter($nodeBin, isset($_SERVER['NODE_PATH']) ? array($_SERVER['NODE_PATH']) : array());
    }

    public function testFilterLoad()
    {
        $asset = new StringAsset('.foo{.bar{width:(1+1);}}');
        $asset->load();

        $testFilePath = '/tmp/lessrecesstestfile.less';
        file_put_contents($testFilePath, $asset->getContent());

        $fileAsset = new FileAsset($testFilePath);
        $fileAsset->load();

        $this->filter->filterLoad($fileAsset);

        $this->assertEquals(".foo .bar {\n  width: 2;\n}", $fileAsset->getContent(), '->filterLoad() parses the content');
        unlink($testFilePath);
    }

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

    public function testCompressImport()
    {
        $expected = <<<EOF
.foo{color:blue}.foo{color:red}
EOF;

        $asset = new FileAsset(__DIR__.'/fixtures/less/main.less');
        $asset->load();

        $this->filter->addParserOption('compress', true);
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() sets an include path based on source url');
    }

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

        $testFilePath = '/tmp/lessrecesstestfile.less';
        file_put_contents($testFilePath, $asset->getContent());

        $fileAsset = new FileAsset($testFilePath);
        $fileAsset->load();

        $this->filter->filterLoad($fileAsset);

        $this->assertEquals($expected, $fileAsset->getContent(), '->filterLoad() adds load paths to include paths');
    }

    public function testSettingLoadPaths()
    {
        $expected = <<<EOF
.foo {
  color: blue;
}

.foo {
  color: red;
}

.bar {
  color: #ff0000;
}
EOF;

        $this->filter->setLoadPaths(array(
            __DIR__.'/fixtures/less',
            __DIR__.'/fixtures/less/import_path',
        ));

        $asset = new StringAsset('@import "main"; @import "_import"; .bar {color: @red}');
        $asset->load();

        $testFilePath = '/tmp/lessrecesstestfile.less';
        file_put_contents($testFilePath, $asset->getContent());

        $fileAsset = new FileAsset($testFilePath);
        $fileAsset->load();

        $this->filter->filterLoad($fileAsset);

        $this->assertEquals($expected, $fileAsset->getContent(), '->filterLoad() sets load paths to include paths');
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
