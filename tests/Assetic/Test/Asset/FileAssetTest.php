<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\FileAsset;

class FileAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $asset, 'Asset implements AssetInterface');
    }
}
