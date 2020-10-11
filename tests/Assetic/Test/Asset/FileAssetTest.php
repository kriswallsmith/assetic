<?php namespace Assetic\Test\Asset;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Asset\FileAsset;

class FileAssetTest extends TestCase
{
    public function testInterface()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertInstanceOf(AssetInterface::class, $asset, 'Asset implements AssetInterface');
    }

    public function testLazyLoading()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertEmpty($asset->getContent(), 'The asset content is empty before load');

        $asset->load();
        $this->assertNotEmpty($asset->getContent(), 'The asset content is not empty after load');
    }

    public function testGetLastModifiedType()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertIsInt($asset->getLastModified(), '->getLastModified() returns an integer');
    }

    public function testGetLastModifiedTypeFileNotFound()
    {
        $asset = new FileAsset(__DIR__."/foo/bar/baz.css");

        $this->expectException("RuntimeException", "The source file");
        $asset->getLastModified();
    }

    public function testGetLastModifiedValue()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertLessThan(time(), $asset->getLastModified(), '->getLastModified() returns the mtime');
    }

    public function testDefaultBaseAndPath()
    {
        $asset = new FileAsset(__FILE__);
        $this->assertEquals(__DIR__, $asset->getSourceRoot(), '->__construct() defaults base to the asset directory');
        $this->assertEquals(basename(__FILE__), $asset->getSourcePath(), '->__construct() defaults path to the asset basename');
        $this->assertEquals(__DIR__, $asset->getSourceDirectory(), '->__construct() derives the asset directory');
    }

    public function testPathGuessing()
    {
        $asset = new FileAsset(__FILE__, [], __DIR__);
        $this->assertEquals(basename(__FILE__), $asset->getSourcePath(), '->__construct() guesses the asset path');
        $this->assertEquals(__DIR__, $asset->getSourceDirectory(), '->__construct() derives the asset directory');
    }

    public function testInvalidBase()
    {
        $this->expectException(\InvalidArgumentException::class);

        $asset = new FileAsset(__FILE__, [], __DIR__.'/foo');
    }
}
