<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\HttpAsset;

class HttpAssetTest extends \PHPUnit_Framework_TestCase
{
    const JQUERY = 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js';

    /**
     * @group http
     */
    public function testGetLastModified()
    {
        $asset = new HttpAsset(self::JQUERY);
        $this->assertInternalType('integer', $asset->getLastModified(), '->getLastModified() returns an integer');
    }

    /**
     * @group http
     */
    public function testProtocolRelativeUrl()
    {
        $asset = new HttpAsset(substr(self::JQUERY, 5));
        $asset->load();
        $this->assertNotEmpty($asset->getContent());
    }

    public function testMalformedUrl()
    {
        $this->setExpectedException('InvalidArgumentException');

        new HttpAsset(__FILE__);
    }

    public function testInvalidUrl()
    {
        $this->setExpectedException('RuntimeException');

        $asset = new HttpAsset('http://invalid.com/foobar');
        $asset->load();
    }

    public function testSourceMetadata()
    {
        $asset = new HttpAsset(self::JQUERY);
        $this->assertEquals('http://ajax.googleapis.com', $asset->getSourceRoot(), '->__construct() set the source root');
        $this->assertEquals('ajax/libs/jquery/1.6.1/jquery.min.js', $asset->getSourcePath(), '->__construct() set the source path');
        $this->assertEquals('http://ajax.googleapis.com/ajax/libs/jquery/1.6.1', $asset->getSourceDirectory(), '->__construct() sets the source directory');
    }
}
