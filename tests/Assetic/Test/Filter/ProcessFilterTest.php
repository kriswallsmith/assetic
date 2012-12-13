<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Filter\ProcessFilter;
use Assetic\Asset\AssetInterface;

class ProcessFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new ProcessFilterForTest();
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'ProcessFilter implements FilterInterface');
    }
}

class ProcessFilterForTest extends ProcessFilter
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
