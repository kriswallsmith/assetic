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

use Assetic\Asset\StringAsset;
use Assetic\Asset\AssetCollection;
use Assetic\Filter\CallablesFilter;

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

    public function testSetContent()
    {
        $coll = new AssetCollection();
        $coll->setContent('asdf');

        $this->assertEquals('asdf', $coll->getContent(), '->setContent() sets the content');
    }

    /**
     * @dataProvider getTimestampsAndExpected
     */
    public function testGetLastModified($timestamps, $expected)
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        for ($i = 0; $i < count($timestamps); $i++) {
            $asset->expects($this->at($i))
                ->method('getLastModified')
                ->will($this->returnValue($timestamps[$i]));
        }

        $coll = new AssetCollection(array_fill(0, count($timestamps), $asset));

        $this->assertEquals($expected, $coll->getLastModified(), '->getLastModifed() returns the highest last modified');
    }

    public function getTimestampsAndExpected()
    {
        return array(
            array(array(1, 2, 3), 3),
            array(array(5, 4, 3), 5),
            array(array(3, 8, 5), 8),
            array(array(3, 8, null), 8),
        );
    }

    public function testRecursiveIteration()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $coll3 = new AssetCollection(array($asset, $asset));
        $coll2 = new AssetCollection(array($asset, $coll3));
        $coll1 = new AssetCollection(array($asset, $coll2));

        $i = 0;
        foreach ($coll1 as $a) {
            $i++;
        }

        $this->assertEquals(4, $i, 'iteration with a recursive iterator is recursive');
    }

    public function testIteration()
    {
        $asset1 = new StringAsset('asset1', 'foo.css');
        $asset2 = new StringAsset('asset2', 'foo.css');
        $asset3 = new StringAsset('asset3', 'bar.css');

        $coll = new AssetCollection(array($asset1, $asset2, $asset3));

        $count = 0;
        foreach ($coll as $a) {
            ++$count;
        }

        $this->assertEquals(2, $count, 'iterator filters duplicates based on url');
    }
}
