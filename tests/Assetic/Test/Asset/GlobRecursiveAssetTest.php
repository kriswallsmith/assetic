<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\GlobRecursiveAsset;

class GlobRecursiveAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $asset = new GlobRecursiveAsset(__DIR__.'/*.php');
        $this->assertInstanceOf('Assetic\\Asset\\AssetInterface', $asset, 'Asset implements AssetInterface');
    }

    public function testIteration()
    {
        $assets = new GlobRecursiveAsset(__DIR__.'/*.php');
        $this->assertGreaterThan(0, iterator_count($assets), 'GlobAsset initializes for iteration');
    }

    public function testRecursiveIteration()
    {
        $assets = new GlobRecursiveAsset(__DIR__.'/*.php');
        $this->assertGreaterThan(0, iterator_count($assets), 'GlobAsset initializes for recursive iteration');
    }

    public function testGetLastModifiedType()
    {
        $assets = new GlobRecursiveAsset(__DIR__.'/*.php');
        $this->assertInternalType('integer', $assets->getLastModified(), '->getLastModified() returns an integer');
    }

    public function testGetLastModifiedValue()
    {
        $assets = new GlobRecursiveAsset(__DIR__.'/*.php');
        $this->assertLessThan(time(), $assets->getLastModified(), '->getLastModified() returns a file mtime');
    }

    public function testLoad()
    {
        $assets = new GlobRecursiveAsset(__DIR__.'/*.php');
        $assets->load();

        $this->assertNotEmpty($assets->getContent(), '->load() loads contents');
    }

    /**
     * Tests that assets are loaded recursively
     */
    public function testDump()
    {
        $assets = new GlobRecursiveAsset(__DIR__.'/../Fixture/*');

        $concatenatedContent = <<<DATA
var messages = {"text.greeting": "Hallo %name%!"};
var messages = {"text.greeting": "Hello %name%!"};
var messages = {"text.greet": "All\u00f4 %name%!"};
var messages = {"text.greeting": "Привет %name%!"};
DATA;
        $concatenatedContent = str_replace("\r", "", $concatenatedContent);

        $this->assertEquals($assets->dump(), $concatenatedContent);
    }
}
