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

use Assetic\Asset\StringAsset;
use Assetic\Filter\GssFilter;

/**
 * @group integration
 */
class GssFilterTest extends FilterTestCase
{
    protected function setUp()
    {
        if (!$javaBin = $this->findExecutable('java', 'JAVA_BIN')) {
            $this->markTestSkipped('Unable to find `java` executable.');
        }

        if (!isset($_SERVER['GSS_JAR'])) {
            $this->markTestSkipped('There is no GSS_JAR environment variable.');
        }

        $this->filter = new GssFilter($_SERVER['GSS_JAR'], $javaBin);
    }

    public function testCompile()
    {
        $input = <<<EOF
@def BG_COLOR rgb(235, 239, 249);
body {background-color: BG_COLOR;}
EOF;

        $expected = <<<EOF
body{background-color:#ebeff9}
EOF;

        $asset = new StringAsset($input);
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent());
    }
}
