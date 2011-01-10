<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\AssetReference;

class AssetReferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $ref = new AssetReference($this->getMock('Assetic\\AssetManager'), 'some_asset');
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $ref, 'AssetReference implements AssetInterface');
    }
}
