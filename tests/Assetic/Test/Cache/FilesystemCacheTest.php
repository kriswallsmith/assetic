<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Cache;

use Assetic\Cache\FilesystemCache;

class FilesystemCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testWithExistingDir()
    {
        $dir = sys_get_temp_dir().'/assetic_filesystemcachetest_testcache';
        $this->removeDir($dir);
        mkdir($dir);

        $cache = new FilesystemCache($dir);

        $this->assertFalse($cache->has('foo'));

        $cache->set('foo', 'bar');
        $this->assertEquals('bar', $cache->get('foo'));

        $this->assertTrue($cache->has('foo'));

        $cache->remove('foo');
        $this->assertFalse($cache->has('foo'));

        $this->removeDir($dir);
    }

    public function testSetCreatesDir()
    {
        $dir = sys_get_temp_dir().'/assetic/fscachetest';
        $this->removeDir($dir);

        $cache = new FilesystemCache($dir);
        $cache->set('foo', 'bar');

        $this->assertFileExists($dir.'/foo');

        $this->removeDir($dir);
        rmdir(sys_get_temp_dir().'/assetic');
    }

    private function removeDir($dir)
    {
        array_map('unlink', glob($dir.'/*'));

        if (is_dir($dir)) {
            rmdir($dir);
        }
    }
}
