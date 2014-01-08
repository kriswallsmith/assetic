<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter\Sass;

use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\Sass\SassFilter;
use Assetic\Test\Filter\FilterTestCase;

class SassFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        $rubyBin = $this->findExecutable('ruby', 'RUBY_BIN');
        if (!$sassBin = $this->findExecutable('sass', 'SASS_BIN')) {
            $this->markTestSkipped('Unable to locate `sass` executable.');
        }

        $this->filter = new SassFilter($sassBin, $rubyBin);
    }

    protected function tearDown()
    {
        $this->filter = null;
    }

    /**
     * @group integration
     */
    public function testSass()
    {
        $input = <<<EOF
body
  color: #F00
EOF;

        $asset = new StringAsset($input);
        $asset->load();

        $this->filter->setStyle(SassFilter::STYLE_COMPACT);
        $this->filter->filterLoad($asset);

        $this->assertEquals("body { color: red; }\n", $asset->getContent(), '->filterLoad() parses the sass');
    }

    /**
     * @group integration
     */
    public function testScssGuess()
    {
        $input = <<<'EOF'
$red: #F00;

.foo {
    color: $red;
}

EOF;

        $expected = '.foo { color: red; }';

        $asset = new StringAsset($input, array(), null, 'foo.scss');
        $asset->load();

        $this->filter->setStyle(SassFilter::STYLE_COMPACT);
        $this->filter->filterLoad($asset);

        $this->assertEquals(".foo { color: red; }\n", $asset->getContent(), '->filterLoad() detects SCSS based on source path extension');
    }

    public function testGetChildrenCatchesSassImports()
    {
        $factory = new AssetFactory('/'); // the factory root isn't used

        $children = $this->filter->getChildren($factory, '@import "include";', __DIR__.'/../fixtures/sass');
        $this->assertCount(1, $children);
        $this->assertEquals(__DIR__.'/../fixtures/sass', $children[0]->getSourceRoot());
        $this->assertEquals('_include.scss', $children[0]->getSourcePath());

        $filters = $children[0]->getFilters();
        $this->assertCount(1, $filters);
        $this->assertInstanceOf('Assetic\Filter\Sass\SassFilter', $filters[0]);
    }

    public function testGetChildrenCatchesPartialsInSubfolders()
    {
        $factory = new AssetFactory('/'); // the factory root isn't used

        $children = $this->filter->getChildren($factory, '@import "import_path/import";', __DIR__.'/../fixtures/sass');
        $this->assertCount(1, $children);
        $this->assertEquals(__DIR__.'/../fixtures/sass', $children[0]->getSourceRoot());
        $this->assertEquals('import_path/_import.scss', $children[0]->getSourcePath());
    }

    public function testGetChildrenIgnoresCssImports()
    {
        // These aren't ignored yet (todo):
        // @import url(main);
        // @import "main" screen;
        $imports = <<<CSS
@import "main.css";
@import "http://foo.com/bar";
CSS;

        $factory = new AssetFactory('/'); // the factory root isn't used

        $this->assertEquals(array(), $this->filter->getChildren($factory, $imports, __DIR__.'/../fixtures/sass'));
    }
}
