<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\AssetReference;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class AssetReferenceTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $ref = new AssetReference($this->getMock('Assetic\\AssetManager'), 'some_asset');
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $ref, 'AssetReference implements AssetInterface');
    }

    public function testResolve()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');
        $am = $this->getMock('Assetic\\AssetManager');
        $name = 'some_asset';

        $am->expects($this->once())
            ->method('get')
            ->with($name)
            ->will($this->returnValue($asset));

        $ref = new AssetReference($am, $name);
        $this->assertSame($asset, $ref->resolve(), '->resolve() pulls the original asset from the manager');
    }
}
