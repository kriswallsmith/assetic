<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\GlobAsset;

class GlobAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $asset = new GlobAsset(__DIR__.'/*.php');
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $asset, 'Asset implements AssetInterface');
    }

    public function testBaseDir()
    {
        $assets = new GlobAsset(__DIR__.'/*.php', array(), __DIR__);
        foreach ($assets as $asset) {
            $this->assertRegExp('/^\w+\.php$/', $asset->getSourceUrl(), 'GlobAsset uses the base directory to determine URL');
        }
    }

    /**
     * @dataProvider provideInvalidBaseDirs
     */
    public function testInvalidBaseDir($baseDir)
    {
        $assets = new GlobAsset(__DIR__.'/*.php', array(), $baseDir);
        foreach ($assets as $asset) {
            $this->assertNull($asset->getSourceUrl(), 'GlobAsset does not set URL when provided an invalid base directory');
        }
    }

    public function provideInvalidBaseDirs()
    {
        return array(
            array(sys_get_temp_dir().'/'),
            array(null),
        );
    }

    public function testIteration()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertGreaterThan(0, iterator_count($assets), 'GlobAsset initializes for iteration');
    }

    public function testRecursiveIteration()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertGreaterThan(0, iterator_count($assets), 'GlobAsset initializes for recursive iteration');
    }

    public function testGetLastModifiedType()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertInternalType('integer', $assets->getLastModified(), '->getLastModified() returns an integer');
    }

    public function testGetLastModifiedValue()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertLessThan(time(), $assets->getLastModified(), '->getLastModified() returns a file mtime');
    }

    public function testLoad()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $assets->load();

        $this->assertNotEmpty($assets->getContent(), '->load() loads contents');
    }

    public function testDump()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertNotEmpty($assets->dump(), '->dump() dumps contents');
    }
}
