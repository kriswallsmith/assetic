<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\AssetReference;
use Assetic\Asset\StringAsset;

class AssetReferenceTest extends \PHPUnit_Framework_TestCase
{
    private $am;
    private $ref;

    protected function setUp()
    {
        $this->am = $this->getMock('Assetic\\AssetManager');
        $this->ref = new AssetReference($this->am, 'foo');
    }

    /**
     * @dataProvider getMethodAndRetVal
     */
    public function testMethods($method, $returnValue)
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method($method)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->ref->$method(), '->'.$method.'() returns the asset value');
    }

    public function getMethodAndRetVal()
    {
        return array(
            array('getContent', 'asdf'),
            array('getSourceRoot', 'asdf'),
            array('getSourcePath', 'asdf'),
            array('getTargetPath', 'asdf'),
            array('getLastModified', 123),
        );
    }

    public function testLazyFilters()
    {
        $this->am->expects($this->never())->method('get');
        $this->ref->ensureFilter($this->getMock('Assetic\\Filter\\FilterInterface'));
    }

    public function testFilterFlush()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())->method('ensureFilter');
        $asset->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));

        $this->ref->ensureFilter($this->getMock('Assetic\\Filter\\FilterInterface'));

        $this->assertInternalType('array', $this->ref->getFilters(), '->getFilters() flushes and returns filters');
    }

    public function testSetContent()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('setContent')
            ->with('asdf');

        $this->ref->setContent('asdf');
    }

    public function testLoad()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('load')
            ->with($filter);

        $this->ref->load($filter);
    }

    public function testDump()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('dump')
            ->with($filter);

        $this->ref->dump($filter);
    }

    public function testClone()
    {
        $filter1 = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter2 = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter3 = $this->getMock('Assetic\\Filter\\FilterInterface');

        $asset = new StringAsset('');
        $this->am->expects($this->any())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));

        $this->ref->ensureFilter($filter1);
        $this->ref->load();

        $clone1 = clone $this->ref;
        $clone1->ensureFilter($filter2);
        $clone1->load();

        $clone2 = clone $clone1;
        $clone2->ensureFilter($filter3);
        $clone2->load();

        $this->assertCount(1, $asset->getFilters());
        $this->assertCount(1, $this->ref->getFilters());
        $this->assertCount(2, $clone1->getFilters());
        $this->assertCount(3, $clone2->getFilters());
    }
}
