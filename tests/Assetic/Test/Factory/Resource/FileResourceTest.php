<?php namespace Assetic\Test\Factory\Resource;

use PHPUnit\Framework\TestCase;
use Assetic\Factory\Resource\FileResource;

class FileResourceTest extends TestCase
{
    public function testIsFresh()
    {
        $resource = new FileResource(__FILE__);
        $this->assertTrue($resource->isFresh(time() + 5));
        $this->assertFalse($resource->isFresh(0));
    }

    public function testGetContent()
    {
        $resource = new FileResource(__FILE__);
        $this->assertEquals(file_get_contents(__FILE__), $resource->getContent());
    }

    public function testIsFreshOnInvalidPath()
    {
        $resource = new FileResource(__FILE__.'foo');
        $this->assertFalse($resource->isFresh(time()), '->isFresh() returns false if the file does not exist');
    }

    public function testGetContentOnInvalidPath()
    {
        $resource = new FileResource(__FILE__.'foo');
        $this->assertSame('', $resource->getContent(), '->getContent() returns an empty string when path is invalid');
    }
}
