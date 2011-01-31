<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\FileAsset;

class FileAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $asset, 'Asset implements AssetInterface');
    }

    public function testLazyLoading()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertEmpty($asset->getContent(), 'The asset content is empty before load');

        $asset->load();
        $this->assertNotEmpty($asset->getContent(), 'The asset content is not empty after load');
    }

    public function testGetLastModifiedType()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertInternalType('integer', $asset->getLastModified(), '->getLastModified() returns an integer');
    }

    public function testGetLastModifiedValue()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertLessThan(time(), $asset->getLastModified(), '->getLastModified() returns the mtime');
    }
}
