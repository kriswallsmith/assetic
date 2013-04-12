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
}
