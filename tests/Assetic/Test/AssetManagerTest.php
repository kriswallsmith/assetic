<?php namespace Assetic\Test;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\AssetManager;

class AssetManagerTest extends TestCase
{
    /** @var AssetManager */
    private $am;

    protected function setUp(): void
    {
        $this->am = new AssetManager();
    }

    public function testGetAsset()
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();
        $this->am->set('foo', $asset);
        $this->assertSame($asset, $this->am->get('foo'), '->get() returns an asset');
    }

    public function testGetInvalidAsset()
    {
        $this->expectException('InvalidArgumentException');
        $this->am->get('foo');
    }

    public function testHas()
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();
        $this->am->set('foo', $asset);

        $this->assertTrue($this->am->has('foo'), '->has() returns true if the asset is set');
        $this->assertFalse($this->am->has('bar'), '->has() returns false if the asset is not set');
    }

    public function testInvalidName()
    {
        $this->expectException('InvalidArgumentException');

        $this->am->set('@foo', $this->getMockBuilder(AssetInterface::class)->getMock());
    }

    public function testClear()
    {
        $this->am->set('foo', $this->getMockBuilder(AssetInterface::class)->getMock());
        $this->am->clear();

        $this->assertFalse($this->am->has('foo'));
    }
}
