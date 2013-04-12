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

use Assetic\Asset\StringAsset;
use Assetic\Filter\Sass\SassFilter;
use Assetic\Test\Filter\FilterTestCase;

/**
 * @group integration
 */
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
}
