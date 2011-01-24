<?php

namespace Assetic\Test;

use Assetic\FilterManager;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class FilterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidName()
    {
        $this->setExpectedException('InvalidArgumentException');

        $fm = new FilterManager();
        $fm->get('foo');
    }

    public function testGetFilter()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $name = 'foo';

        $fm = new FilterManager();
        $fm->set($name, $filter);

        $this->assertSame($filter, $fm->get($name), '->set() sets a filter');
    }

    public function testHas()
    {
        $fm = new FilterManager();
        $fm->set('foo', $this->getMock('Assetic\\Filter\\FilterInterface'));
        $this->assertTrue($fm->has('foo'), '->has() returns true if the filter is set');
    }

    public function testHasInvalid()
    {
        $fm = new FilterManager();
        $this->assertFalse($fm->has('foo'), '->has() returns false if the filter is not set');
    }
}
