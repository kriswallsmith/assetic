<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset\Iterator;

use Assetic\Asset\AssetReference;
use Assetic\Asset\FileAsset;
use Assetic\Asset\Iterator\AssetCollectionIterator;

class AssetCollectionIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testAppliesAssetReferenceNameToTargetPath()
    {
        $asset = new FileAsset('/path/to/asset');

        $am = $this->getMock('Assetic\AssetManager');
        $am->expects($this->any())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));

        $reference = new AssetReference($am, 'foo');

        $collection = $this->getMock('Assetic\Asset\AssetCollectionInterface');
        $collection->expects($this->once())
            ->method('all')
            ->will($this->returnValue(array($reference)));
        $collection->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));
        $collection->expects($this->once())
            ->method('getVars')
            ->will($this->returnValue(array()));

        $iterator = new AssetCollectionIterator($collection, new \SplObjectStorage());
        $asset = $iterator->current();

        $this->assertSame('_foo_1', $asset->getTargetPath());
    }
}
