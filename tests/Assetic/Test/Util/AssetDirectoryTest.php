<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Util;

use Assetic\Cache\ArrayCache;

class AssetDirectoryTest extends \PHPUnit_Framework_TestCase
{
    protected $toDelete = array();

    public function testFilePublication()
    {
        $dir            = $this->createAssetDirectory();
        $file           = $this->createFile('foo');
        $expectedTarget = $dir->getDirectory().'/'.basename($file);

        $path = $dir->add($file);
        $this->assertTrue(file_exists($expectedTarget), "File should be copied on disk");
        $this->assertEquals("foo", file_get_contents($expectedTarget), "File content should be correct");
    }

    public function testFileNotAddedTwice()
    {
        $dir    = $this->createAssetDirectory();
        $file   = $this->createFile('foo');

        $this->assertEquals($dir->add($file), $dir->add($file), "Path should be the same");
    }

    public function __destruct()
    {
        foreach ($this->toDelete as $path) {
            if (!file_exists($path) || !is_dir($path)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }
        }
    }

    private function createFile($content)
    {
        $file = tempnam($dir = sys_get_temp_dir(), 'assetic_');
        file_put_contents($file, $content);
        $toDelete[] = $file;

        return $file;
    }

    private function createAssetDirectory($cache = true)
    {
        $dir = tempnam(sys_get_temp_dir(), 'assetic_');
        unlink($dir);

        if ($cache) {
            $cache = new ArrayCache();
        }

        $this->toDelete[] = $dir;

        return new AssetDirectory($dir, null, $cache);
    }
}
