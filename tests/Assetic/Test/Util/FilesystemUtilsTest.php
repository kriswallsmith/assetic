<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Util;

use Assetic\Util\FilesystemUtils;

class FilesystemUtilsTest extends \PHPUnit_Framework_TestCase
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
        $filePath = FilesystemUtils::createTemporaryFileAndWrite(__METHOD__, 'hello world');
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
