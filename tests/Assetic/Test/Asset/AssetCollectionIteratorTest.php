<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\StringAsset;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetCollectionIterator;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class AssetCollectionIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group functional
     */
    public function testIteration()
    {
        $asset1 = new StringAsset('asset1', 'foo.bar');
        $asset2 = new StringAsset('asset2', 'foo.bar');
        $asset3 = new StringAsset('asset3', 'foo.bar.baz');

        $coll = new AssetCollection(array($asset1, $asset2, $asset3));
        $it = new AssetCollectionIterator($coll);

        $count = 0;
        foreach ($it as $a) {
            ++$count;
        }

        $this->assertEquals(2, $count, 'iterator filters duplicates based on url');
    }
}
