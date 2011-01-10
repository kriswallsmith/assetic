<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\Asset;
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
        $asset = new Asset('');
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');

        $filter->expects($this->once())
            ->method('filterLoad')
            ->with($asset);

        $coll = new AssetCollection(array($asset), array($filter));
        $coll->load();
    }

    /**
     * @group functional
     */
    public function testDumpFilter()
    {
        $asset = new Asset('');
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');

        $filter->expects($this->once())
            ->method('filterDump')
            ->with($asset);

        $coll = new AssetCollection(array($asset), array($filter));
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

        $innerColl = new AssetCollection(array(new Asset($content)));
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
        $asset = new Asset('asset');
        $nestedAsset = new Asset('nested');
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
     * @dataProvider provideForLoadAssets
     * @group functional
     */
    public function testLoadAssets($contents, $glue, $expected)
    {
        $assets = array();
        foreach ($contents as $content) {
            $assets[] = new Asset($content);
        }

        $coll = new AssetCollection($assets);
        $coll->load($glue);

        $this->assertEquals($expected, $coll->getContent(), '->load() merges the content of the assets');
    }

    public function provideForLoadAssets()
    {
        return array(
            array(array('asdf1', 'asdf2'), ' ', 'asdf1 asdf2'),
            array(array('foo1', 'foo2'), "\n", "foo1\nfoo2"),
        );
    }
}
