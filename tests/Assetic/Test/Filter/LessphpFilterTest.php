<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\LessphpFilter;

/**
 * @group integration
 * @property LessphpFilter $filter
 */
class LessphpFilterTest extends LessFilterTest
{
    protected function setUp()
    {
        $this->filter = new LessphpFilter();
    }

    public function testPresets()
    {
        $expected = <<<EOF
.foo {
  color: green;
}

EOF;

        $asset = new StringAsset('.foo { color: @bar }');
        $asset->load();

        $this->filter->setPresets(array(
            'bar' => 'green'
        ));

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->setPresets() to pass variables into lessphp filter');
    }

    public function testFormatterLessjs()
    {
        $expected = ".foo {\n  color: green;\n}\n";

        $asset = new StringAsset('.foo { color: green; }');
        $asset->load();

        $this->filter->setFormatter('lessjs');
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->setFormatter("lessjs")');
    }

    public function testFormatterCompressed()
    {
        $expected = '.foo{color:green;}';

        $asset = new StringAsset('.foo { color: green; }');
        $asset->load();

        $this->filter->setFormatter('compressed');
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->setFormatter("compressed")');
    }

    public function testFormatterClassic()
    {
        $expected = ".foo { color:green; }\n";

        $asset = new StringAsset('.foo { color: green; }');
        $asset->load();

        $this->filter->setFormatter('classic');
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->setFormatter("classic")');
    }
}
