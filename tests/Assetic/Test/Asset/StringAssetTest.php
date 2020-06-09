<?php namespace Assetic\Test\Asset;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Asset\StringAsset;

class StringAssetTest extends TestCase
{
    public function testInterface()
    {
        $asset = new StringAsset('');
        $this->assertInstanceOf(AssetInterface::class, $asset, 'Asset implements AssetInterface');
    }

    public function testLoadAppliesFilters()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $filter->expects($this->once())->method('filterLoad');

        $asset = new StringAsset('foo', array($filter));
        $asset->load();
    }

    public function testAutomaticLoad()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $filter->expects($this->once())->method('filterLoad');

        $asset = new StringAsset('foo', array($filter));
        $asset->dump();
    }

    public function testGetFilters()
    {
        $asset = new StringAsset('');
        $this->assertIsArray($asset->getFilters(), '->getFilters() returns an array');
    }

    public function testLoadAppliesAdditionalFilter()
    {
        $asset = new StringAsset('');
        $asset->load();

        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $filter->expects($this->once())
            ->method('filterLoad')
            ->with($asset);

        $asset->load($filter);
    }

    public function testDumpAppliesAdditionalFilter()
    {
        $asset = new StringAsset('');

        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $filter->expects($this->once())
            ->method('filterDump')
            ->with($asset);

        $asset->dump($filter);
    }

    public function testLastModified()
    {
        $asset = new StringAsset('');
        $asset->setLastModified(123);
        $this->assertEquals(123, $asset->getLastModified(), '->getLastModified() return the set last modified value');
    }

    public function testGetContentNullUnlessLoaded()
    {
        // see https://github.com/kriswallsmith/assetic/pull/432
        $asset = new StringAsset("test");
        $this->assertNull($asset->getContent(), '->getContent() returns null unless load() has been called.');

        $asset->load();

        $this->assertEquals("test", $asset->getContent(), '->getContent() returns the content after load()');
    }
}
