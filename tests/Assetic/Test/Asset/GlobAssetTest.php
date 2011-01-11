<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\GlobAsset;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class GlobAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $asset = new GlobAsset(__DIR__.'/*.php');
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $asset, 'Asset implements AssetInterface');
    }
}
