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
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filterLoad');

        $coll = new AssetCollection(array(new Asset('')), array($filter));
        $coll->load();
    }

    /**
     * @group functional
     */
    public function testDumpFilter()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $filter->expects($this->once())->method('filterDump');

        $coll = new AssetCollection(array(new Asset('')), array($filter));
        $coll->dump();
    }

    /**
     * @group functional
     */
    public function testNestedCollectionLoad()
    {
        $body = 'foobar';

        $count = 0;
        $matches = array();
        $filter = new CallablesFilter(function($asset) use ($body, & $matches, & $count)
        {
            ++$count;
            if ($body == $asset->getBody()) {
                $matches[] = $asset;
            }
        });

        $innerColl = new AssetCollection(array(new Asset($body)));
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

        $bodys = array();
        $filter = new CallablesFilter(function($asset) use(& $bodys)
        {
            $bodys[] = $asset->getBody();
        });

        $coll = new AssetCollection(array($asset, $innerColl), array($filter));
        $coll->load();

        $this->assertEquals(array('asset', 'nested'), $bodys, '->load() iterates over multiple levels');
    }
}
