<?php

namespace Assetic\Test\Filter\Yui;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\Yui\BaseCompressorFilter;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class BaseCompressorFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new YuiCompressorFilterForTest('/path/to/jar');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'BaseCompressorFilter implements FilterInterface');
    }
}

class YuiCompressorFilterForTest extends BaseCompressorFilter
{
    public function filterDump(AssetInterface $asset, $targetUrl = null)
    {
    }
}
