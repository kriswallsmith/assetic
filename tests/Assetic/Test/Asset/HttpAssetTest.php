<?php namespace Assetic\Test\Asset;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\HttpAsset;

class HttpAssetTest extends TestCase
{
    const JQUERY = 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js';

    /**
     * @group http
     */
    public function testGetLastModified()
    {
        $asset = new HttpAsset(self::JQUERY);
        $this->assertIsInt($asset->getLastModified(), '->getLastModified() returns an integer');
    }

    /**
     * @group http
     */
    public function testProtocolRelativeUrl()
    {
        $asset = new HttpAsset(substr(self::JQUERY, 5));
        $asset->load();
        $this->assertNotEmpty($asset->getContent());
    }

    public function testMalformedUrl()
    {
        $this->expectException(\InvalidArgumentException::class);

        new HttpAsset(__FILE__);
    }

    public function testInvalidUrl()
    {
        $this->expectException(\Throwable::class);

        $asset = new HttpAsset('http://invalid.com/foobar');
        $asset->load();
    }

    public function testSourceMetadata()
    {
        $asset = new HttpAsset(self::JQUERY);
        $this->assertEquals('http://ajax.googleapis.com', $asset->getSourceRoot(), '->__construct() set the source root');
        $this->assertEquals('ajax/libs/jquery/1.6.1/jquery.min.js', $asset->getSourcePath(), '->__construct() set the source path');
        $this->assertEquals('http://ajax.googleapis.com/ajax/libs/jquery/1.6.1', $asset->getSourceDirectory(), '->__construct() sets the source directory');
    }
}
