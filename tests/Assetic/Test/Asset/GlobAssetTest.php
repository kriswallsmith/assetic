<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\GlobAsset;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class GlobAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $asset = new GlobAsset(__DIR__.'/*.php');
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $asset, 'Asset implements AssetInterface');
    }

    public function testBaseDir()
    {
        $assets = new GlobAsset(__DIR__.'/*.php', __DIR__);
        foreach ($assets as $asset) {
            $this->assertRegExp('/^\w+\.php$/', $asset->getUrl(), 'GlobAsset uses the base directory to determine URL');
        }
    }

    /**
     * @dataProvider provideInvalidBaseDirs
     */
    public function testInvalidBaseDir($baseDir)
    {
        $assets = new GlobAsset(__DIR__.'/*.php', $baseDir);
        foreach ($assets as $asset) {
            $this->assertNull($asset->getUrl(), 'GlobAsset does not set URL when provided an invalid base directory');
        }
    }

    public function provideInvalidBaseDirs()
    {
        return array(
            array(sys_get_temp_dir().'/'),
            array(null),
        );
    }
}
