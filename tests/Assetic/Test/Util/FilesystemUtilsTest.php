<?php namespace Assetic\Test\Util;

use PHPUnit\Framework\TestCase;
use Assetic\Util\FilesystemUtils;

class FilesystemUtilsTest extends TestCase
{
    public function testGetTemporaryDirectory()
    {
        $this->assertNotEmpty(FilesystemUtils::getTemporaryDirectory());
    }

    public function testCreateThrowAwayDirectory()
    {
        $dirPath = FilesystemUtils::createThrowAwayDirectory(__METHOD__);
        $this->assertDirectoryExists($dirPath);
        rmdir($dirPath);
    }

    public function testCreateTemporaryFile()
    {
        $filePath = FilesystemUtils::createTemporaryFile(__METHOD__);
        $this->assertFileExists($filePath);
        unlink($filePath);
    }

    public function testCreateTemporaryFileAndWrite()
    {
        $filePath = FilesystemUtils::createTemporaryFile(__METHOD__, 'hello world');
        $this->assertFileExists($filePath);
        $result = file_get_contents($filePath);
        $this->assertEquals('hello world', $result);
        unlink($filePath);
    }

    public function testRemoveDirectory()
    {
        $dirPath = FilesystemUtils::createThrowAwayDirectory(__METHOD__);
        file_put_contents($dirPath . DIRECTORY_SEPARATOR . 'test', 'hello world');
        FilesystemUtils::removeDirectory($dirPath);
        $this->assertDirectoryNotExists($dirPath);
    }
}
