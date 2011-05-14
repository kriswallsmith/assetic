<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test;

use Assetic\AssetWriter;

class AssetWriterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->dir = sys_get_temp_dir().'/assetic_tests_'.rand(11111, 99999);
        mkdir($this->dir);
        $this->writer = new AssetWriter($this->dir);
    }

    protected function tearDown()
    {
        array_map('unlink', glob($this->dir.'/*'));
        rmdir($this->dir);
    }

    public function testWriteManagerAssets()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');
        $am = $this->getMock('Assetic\\AssetManager');

        $am->expects($this->once())
            ->method('getNames')
            ->will($this->returnValue(array('foo')));
        $am->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('getTargetPath')
            ->will($this->returnValue('target_url'));
        $asset->expects($this->once())
            ->method('dump')
            ->will($this->returnValue('content'));

        $this->writer->writeManagerAssets($am);

        $this->assertFileExists($this->dir.'/target_url');
        $this->assertEquals('content', file_get_contents($this->dir.'/target_url'));
    }
}
