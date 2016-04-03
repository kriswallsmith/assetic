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

use Assetic\Asset\StringAsset;
use Assetic\Filter\SeparatorFilter;

/**
 * @group integration
 */
class SeparatorFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testAppend()
    {
        $asset = new StringAsset('foobar');
        $asset->load();

        $filter = new SeparatorFilter('+');
        $filter->filterDump($asset);

        $this->assertEquals('foobar+', $asset->getContent());
    }
}
