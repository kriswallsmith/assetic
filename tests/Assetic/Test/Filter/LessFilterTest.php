<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\LessFilter;

class LessFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group integration
     */
    public function testFilterLoad()
    {
        if (!isset($_SERVER['NODE_BIN']) || !isset($_SERVER['NODE_PATH'])) {
            $this->markTestSkipped('No node.js configuration.');
        }

        $asset = new StringAsset('.foo{.bar{width:1+1;}}');
        $asset->load();

        $filter = new LessFilter(__DIR__, $_SERVER['NODE_BIN'], array($_SERVER['NODE_PATH']));
        $filter->filterLoad($asset);

        $this->assertEquals(".foo .bar {\n  width: 2;\n}\n", $asset->getContent(), '->filterLoad() parses the content');
    }
}
