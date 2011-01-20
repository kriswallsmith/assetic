<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\Asset;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetCollectionIterator;

class AssetCollectionIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group functional
     */
    public function testIteration()
    {
        $asset1 = new Asset('asset1', 'foo.bar');
        $asset2 = new Asset('asset2', 'foo.bar');
        $asset3 = new Asset('asset3', 'foo.bar.baz');

        $coll = new AssetCollection(array($asset1, $asset2, $asset3));
        $it = new AssetCollectionIterator($coll);

        $count = 0;
        foreach ($it as $a) {
            ++$count;
        }

        $this->assertEquals(2, $count, 'iterator filters duplicates based on url');
    }
}
