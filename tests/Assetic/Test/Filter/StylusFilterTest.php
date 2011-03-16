<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\StylusFilter;

class StylusFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group integration
     */
    public function testFilterLoad()
    {
        if (!isset($_SERVER['NODE_BIN']) || !isset($_SERVER['NODE_PATH'])) {
            $this->markTestSkipped('No node.js configuration.');
        }

        $asset = new StringAsset("body\n  font 12px Helvetica, Arial, sans-serif\n  color black");
        $asset->load();

        $filter = new StylusFilter(__DIR__, $_SERVER['NODE_BIN'], array($_SERVER['NODE_PATH']));
        $filter->filterLoad($asset);

        $this->assertEquals("body {\n  font: 12px Helvetica, Arial, sans-serif;\n  color: #000;\n}\n", $asset->getContent(), '->filterLoad() parses the content');
    }

    /**
     * @group integration
     */
    public function testFilterLoadWithCompression()
    {
        if (!isset($_SERVER['NODE_BIN']) || !isset($_SERVER['NODE_PATH'])) {
            $this->markTestSkipped('No node.js configuration.');
        }

        $asset = new StringAsset("body\n  font 12px Helvetica, Arial, sans-serif\n  color black;");
        $asset->load();

        $filter = new StylusFilter(__DIR__, $_SERVER['NODE_BIN'], array($_SERVER['NODE_PATH']));
        $filter->setCompress(true);
        $filter->filterLoad($asset);

        $this->assertEquals("body{font:12px Helvetica,Arial,sans-serif;color:#000}\n", $asset->getContent(), '->filterLoad() parses the content and compress it');
    }
}
