<?php

namespace Assetic\Test\Filter\Sass;

use Assetic\Filter\Sass\SassFilter;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SassFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new SassFilter('/path/to/sass');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'SassFilter implements FilterInterface');
    }
}
