<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\StringAsset;
use Assetic\Asset\AssetCollection;
use Assetic\Filter\CallablesFilter;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class AssetCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $coll = new AssetCollection();
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $coll, 'AssetCollection implements AssetInterface');
    }

    /**
     * @group functional
     */
    public function testLoadFilter()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filterLoad');

        $coll = new AssetCollection(array(new StringAsset('')), array($filter));
        $coll->load();
    }

    /**
     * @group functional
     */
    public function testDumpFilter()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filterDump');

        $coll = new AssetCollection(array(new StringAsset('')), array($filter));
        $coll->dump();
    }

    /**
     * @group functional
     */
    public function testNestedCollectionLoad()
    {
        $content = 'foobar';

        $count = 0;
        $matches = array();
        $filter = new CallablesFilter(function($asset) use ($content, & $matches, & $count)
        {
            ++$count;
            if ($content == $asset->getContent()) {
                $matches[] = $asset;
            }
        });

        $innerColl = new AssetCollection(array(new StringAsset($content)));
        $outerColl = new AssetCollection(array($innerColl), array($filter));
        $outerColl->load();

        $this->assertEquals(1, count($matches), '->load() applies filters to leaves');
        $this->assertEquals(1, $count, '->load() applies filters to leaves only');
    }

    /**
     * @group functional
     */
    public function testMixedIteration()
    {
        $asset = new StringAsset('asset');
        $nestedAsset = new StringAsset('nested');
        $innerColl = new AssetCollection(array($nestedAsset));

        $contents = array();
        $filter = new CallablesFilter(function($asset) use(& $contents)
        {
            $contents[] = $asset->getContent();
        });

        $coll = new AssetCollection(array($asset, $innerColl), array($filter));
        $coll->load();

        $this->assertEquals(array('asset', 'nested'), $contents, '->load() iterates over multiple levels');
    }

    /**
     * @group functional
     */
    public function testLoadDuplicates()
    {
        $asset = new StringAsset('asset', 'foo.bar');
        $coll = new AssetCollection(array($asset, $asset));
        $coll->load();

        $this->assertEquals('asset', $coll->getContent(), '->load() detects duplicate assets');
    }

    /**
     * @group functional
     */
    public function testDumpDuplicates()
    {
        $asset = new StringAsset('asset', 'foo.bar');
        $coll = new AssetCollection(array($asset, $asset));
        $coll->load();

        $this->assertEquals('asset', $coll->dump(), '->dump() detects duplicate assets');
    }

    public function testIterationFilters()
    {
        $count = 0;
        $filter = new CallablesFilter(function() use(&$count) { ++$count; });

        $coll = new AssetCollection();
        $coll->add(new StringAsset(''));
        $coll->ensureFilter($filter);

        foreach ($coll as $asset) {
            $asset->dump();
        }

        $this->assertEquals(1, $count, 'collection filters are called when child assets are iterated over');
    }
}
