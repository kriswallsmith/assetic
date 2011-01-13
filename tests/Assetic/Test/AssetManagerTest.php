<?php

namespace Assetic\Test;

use Assetic\AssetManager;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $am = new AssetManager();
        $this->assertInstanceOf('Assetic\\Filter\\Filterable', $am, 'AssetManager implements Filterable');
    }

    public function testGetResolvesReferences()
    {
        $name = 'some_asset';
        $refName = 'some_ref';

        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');
        $ref = $this->getMockBuilder('Assetic\\Asset\\AssetReference')
            ->disableOriginalConstructor()
            ->getMock();

        $ref->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($asset));

        $am = new AssetManager();
        $am->set($name, $asset);
        $am->set($refName, $ref);

        $this->assertSame($asset, $am->get($refName), '->get() resolves asset references');
    }

    public function testGetResolvesReferencesRecursively()
    {
        $name = 'some_asset';
        $refName1 = 'some_ref1';
        $refName2 = 'some_ref2';

        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');
        $ref1 = $this->getMockBuilder('Assetic\\Asset\\AssetReference')
            ->disableOriginalConstructor()
            ->getMock();
        $ref2 = $this->getMockBuilder('Assetic\\Asset\\AssetReference')
            ->disableOriginalConstructor()
            ->getMock();

        $ref1->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($ref2));
        $ref2->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($asset));

        $am = new AssetManager();
        $am->set($name, $asset);
        $am->set($refName1, $ref1);
        $am->set($refName2, $ref2);

        $this->assertSame($asset, $am->get($refName1), '->get() resolves asset references recursively');
    }

    public function testGetDetectsCircularRefs()
    {
        $this->setExpectedException('LogicException');

        $ref = $this->getMockBuilder('Assetic\\Asset\\AssetReference')
            ->disableOriginalConstructor()
            ->getMock();
        $ref->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue($ref));

        $am = new AssetManager();
        $am->set('some_ref', $ref);
        $am->get('some_ref');
    }
}
