<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test;

use Assetic\Asset\FileAsset;

use Assetic\AssetManager;
use Assetic\NonOverwritingAssetWriter;

class NonOverwritingAssetWriterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->dir = sys_get_temp_dir().'/assetic_tests_'.rand(11111, 99999);
        mkdir($this->dir);
        $this->writer = new NonOverwritingAssetWriter($this->dir, array(
            'locale' => array('en', 'de', 'fr'),
            'browser' => array('ie', 'firefox', 'other'),
            'gzip' => array('gzip', '')
        ));
    }

    protected function tearDown()
    {
        array_map('unlink', glob($this->dir.'/*'));
        rmdir($this->dir);
    }

    public function testWritesWhenNonExistend()
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
        $asset->expects($this->atLeastOnce())
            ->method('getTargetPath')
            ->will($this->returnValue('target_url'));
        $asset->expects($this->once())
            ->method('dump')
            ->will($this->returnValue('content'));
        $asset->expects($this->atLeastOnce())
            ->method('getVars')
            ->will($this->returnValue(array()));
        $asset->expects($this->atLeastOnce())
            ->method('getValues')
            ->will($this->returnValue(array()));

        $this->writer->writeManagerAssets($am);

        $this->assertFileExists($this->dir.'/target_url');
        $this->assertEquals('content', file_get_contents($this->dir.'/target_url'));
    }

    public function testNotWritesWhenNotModified()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');
        $am = $this->getMock('Assetic\\AssetManager');

        $am->expects($this->exactly(2))
            ->method('getNames')
            ->will($this->returnValue(array('foo')));
        $am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->atLeastOnce())
            ->method('getTargetPath')
            ->will($this->returnValue('target_url'));
        $asset->expects($this->once())
            ->method('dump')
            ->will($this->returnValue('content'));
        $asset->expects($this->atLeastOnce())
            ->method('getVars')
            ->will($this->returnValue(array()));
        $asset->expects($this->atLeastOnce())
            ->method('getValues')
            ->will($this->returnValue(array()));

        $this->writer->writeManagerAssets($am);
        
        $this->assertFileExists($this->dir.'/target_url');
        $mtime = filemtime($this->dir.'/target_url');
        
        sleep(1);
        
        $this->writer->writeManagerAssets($am);
        $this->assertFileExists($this->dir.'/target_url');
        $this->assertEquals($mtime, filemtime($this->dir.'/target_url'));
    }
}
