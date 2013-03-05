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
use Assetic\Filter\RooleFilter;

/**
 * @group integration
 */
class RooleFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        $rooleBin = $this->findExecutable('roole', 'ROOLE_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$rooleBin) {
            $this->markTestSkipped('Unable to find `roole` executable.');
        }

        $this->filter = new RooleFilter($rooleBin, $nodeBin);
    }

    public function testFilterLoad()
    {
        $source = <<<ROOLE
\$margin = 30px

body
  margin: \$margin

ROOLE;

        $asset = new StringAsset($source);
        $asset->load();

        $this->filter->filterLoad($asset);

        $content = $asset->getContent();
        $this->assertNotContains('$margin', $content);
        $this->assertContains('margin: 30px;', $content);
    }
}
