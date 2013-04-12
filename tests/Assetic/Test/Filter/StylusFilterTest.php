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
use Assetic\Filter\StylusFilter;

/**
 * @group integration
 */
class StylusFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        if (!$nodeBin = $this->findExecutable('node', 'NODE_BIN')) {
            $this->markTestSkipped('Unable to find `node` executable.');
        }

        if (!$this->checkNodeModule('stylus', $nodeBin)) {
            $this->markTestSkipped('The "stylus" module is not installed.');
        }

        $this->filter = new StylusFilter($nodeBin, isset($_SERVER['NODE_PATH']) ? array($_SERVER['NODE_PATH']) : array());
    }

    public function testFilterLoad()
    {
        $asset = new StringAsset("body\n  font 12px Helvetica, Arial, sans-serif\n  color black");
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals("body {\n  font: 12px Helvetica, Arial, sans-serif;\n  color: #000;\n}\n", $asset->getContent(), '->filterLoad() parses the content');
    }

    public function testFilterLoadWithCompression()
    {
        $asset = new StringAsset("body\n  font 12px Helvetica, Arial, sans-serif\n  color black;");
        $asset->load();

        $this->filter->setCompress(true);
        $this->filter->filterLoad($asset);

        $this->assertEquals("body{font:12px Helvetica,Arial,sans-serif;color:#000}\n", $asset->getContent(), '->filterLoad() parses the content and compress it');
    }
}
