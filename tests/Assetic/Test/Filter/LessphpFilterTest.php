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

use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\LessphpFilter;

/**
 * @group integration
 */
class LessphpFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->filter = new LessphpFilter();
    }

    public function testFilterLoad()
    {
        $asset = new StringAsset('.foo{.bar{width:1+ 1;}}');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals(".foo .bar { width:2; }\n", $asset->getContent(), '->filterLoad() parses the content');
    }

    public function testImport()
    {
        $expected = <<<EOF
.foo { color:blue; }
.foo { color:red; }

EOF;

        $asset = new FileAsset(__DIR__.'/fixtures/less/main.less');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() sets an include path based on source url');
    }

    public function testPresets()
    {
        $asset = new StringAsset('.foo { color: @bar }');
        $asset->load();

        $this->filter->setPresets(array(
            'bar' => 'green'
        ));

        $this->filter->filterLoad($asset);

        $this->assertEquals(".foo { color:green; }\n", $asset->getContent(), '->setPresets() to pass variables into lessphp filter');
    }
}
