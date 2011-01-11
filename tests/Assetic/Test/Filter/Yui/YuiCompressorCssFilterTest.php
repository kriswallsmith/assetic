<?php

namespace Assetic\Test\Filter\Yui;

use Assetic\Filter\Yui\YuiCompressorCssFilter;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class YuiCompressorCssFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new YuiCompressorCssFilter('/path/to/jar');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'YuiCompressorCssFilter implements FilterInterface');
    }
}
