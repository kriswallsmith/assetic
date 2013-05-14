<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter\Sass;

use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\Sass\ScssFilter;
use Assetic\Test\Filter\FilterTestCase;

/**
 * @group integration
 */
class ScssFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        $rubyBin = $this->findExecutable('ruby', 'RUBY_BIN');
        if (!$sassBin = $this->findExecutable('sass', 'SASS_BIN')) {
            $this->markTestSkipped('Unable to locate `sass` executable.');
        }

        $this->filter = new ScssFilter($sassBin, $rubyBin);
    }

    protected function tearDown()
    {
        $this->filter = null;
    }

    public function testImport()
    {
        $asset = new FileAsset(__DIR__.'/../fixtures/sass/main.scss');
        $asset->load();

        $this->filter->setStyle(ScssFilter::STYLE_COMPACT);
        $this->filter->filterLoad($asset);

        $expected = <<<EOF
.foo { color: blue; }

.foo { color: red; }

EOF;

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() loads imports');
    }

    public function testLoadPath()
    {
        $expected = <<<EOF
.foo {
  color: blue; }

.foo {
  color: red; }

EOF;

        $this->filter->addLoadPath(__DIR__.'/../fixtures/sass');

        $asset = new StringAsset('@import "main";');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() adds load paths to include paths');
    }

    public function testSettingLoadPaths()
    {
        $expected = <<<EOF
.foo {
  color: blue; }

.foo {
  color: red; }

.bar {
  color: red; }

EOF;

        $this->filter->setLoadPaths(array(
            __DIR__.'/../fixtures/sass',
            __DIR__.'/../fixtures/sass/import_path',
        ));

        $asset = new StringAsset('@import "main"; @import "import"; .bar {color: $red}');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() sets load paths to include paths');
    }
}
