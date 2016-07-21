<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2015 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Factory\AssetFactory;
use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\SassphpFilter;

class SassphpFilterTest extends \PHPUnit_Framework_TestCase
{
    private $filter;

    protected function setUp()
    {
        if (!extension_loaded('sass')) {
            $this->markTestSkipped('Sass extension is not installed');
        }

        $this->filter = new SassphpFilter();
        $this->filter->setOutputStyle(\Sass::STYLE_COMPRESSED);
    }

    public function testCompilation()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/sassphp/example.scss');
        $asset->load();

        $expected = <<<EOF
body{color:red}

EOF;

        $this->filter->filterLoad($asset);
        $this->assertEquals($expected, $asset->getContent());
    }

    public function testOutputStyle()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/sassphp/example.scss');
        $asset->load();

        $expected = <<<EOF
body {
  color: red;
}

EOF;

        $this->filter->setOutputStyle(\Sass::STYLE_EXPANDED);
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent());
    }

    public function testImports()
    {
        $asset = new StringAsset('@import "cheese";');
        $asset->load();

        $expected = <<<EOF
body{color:blue}

EOF;

        $this->filter->addIncludePath(__DIR__ . '/fixtures/sassphp/includes');
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent());
    }

    public function testExtractChildren()
    {
        $factory = new AssetFactory('');

        $children = $this->filter->getChildren($factory, '@import "import";', __DIR__.'/fixtures/sassphp');

        $this->assertCount(2, $children);
    }

    public function testExtractChildrenWithEmptyPath()
    {
        $factory = new AssetFactory(__DIR__.'/fixtures/sassphp');

        $this->filter->addIncludePath(__DIR__.'/fixtures/sassphp');
        $children = $this->filter->getChildren($factory, '@import "import";');

        $this->assertCount(2, $children);
    }
}
