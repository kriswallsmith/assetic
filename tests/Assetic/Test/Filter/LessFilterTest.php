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
    public function testLessc()
    {
        if (!isset($_SERVER['LESSC_PATH'])) {
            $this->markTestSkipped('There is no LESSC_PATH environment variable.');
        }

        $asset = new StringAsset('body{color:red;}');
        $asset->load();

        $filter = new LessFilter($_SERVER['LESSC_PATH']);
        $filter->filterLoad($asset);
        $filter->filterDump($asset);

        $this->assertEquals("body { color: red; }\n", $asset->getContent(), '->filterLoad() parses the content');
    }
}
