<?php

namespace Assetic\Test\Filter;

use Assetic\Filter\CssRewriteFilter;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CssRewriteFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new CssRewriteFilter();
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'CssRewriteFilter implements FilterInterface');
    }
}
