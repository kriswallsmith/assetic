<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Filter\BaseProcessFilter;
use Assetic\Asset\AssetInterface;

class BaseProcessFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new BaseProcessFilterFilter();
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'BaseProcessFilter implements FilterInterface');
    }
}

class BaseProcessFilterFilter extends BaseProcessFilter
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
