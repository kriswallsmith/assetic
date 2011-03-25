<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter\Sass;

use Assetic\Asset\StringAsset;
use Assetic\Filter\Sass\SassFilter;

/**
 * @group integration
 */
class SassFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testSass()
    {
        if (!isset($_SERVER['SASS_BIN'])) {
            $this->markTestSkipped('There is no SASS_BIN environment variable.');
        }

        $input = <<<EOF
body
  color: #F00
EOF;

        $asset = new StringAsset($input);
        $asset->load();

        $filter = new SassFilter($_SERVER['SASS_BIN']);
        $filter->setStyle(SassFilter::STYLE_COMPACT);
        $filter->filterLoad($asset);
        $filter->filterDump($asset);

        $this->assertEquals("body { color: red; }\n", $asset->getContent(), '->filterLoad() parses the sass');
    }
}
