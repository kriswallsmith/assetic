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
    const ASSET_URL = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css';

    /**
     * @group http
     */
    public function testGetLastModified()
    {
        $asset = new HttpAsset(self::ASSET_URL);
        $this->assertInternalType('integer', $asset->getLastModified(), '->getLastModified() returns an integer');
    }

    /**
     * @group http
     */
    public function testProtocolRelativeUrl()
    {
        $asset = new HttpAsset(substr(self::ASSET_URL, 6));
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
        $asset = new HttpAsset(self::ASSET_URL);
        $this->assertEquals('https://maxcdn.bootstrapcdn.com', $asset->getSourceRoot(), '->__construct() set the source root');
        $this->assertEquals('bootstrap/3.3.7/css/bootstrap.min.css', $asset->getSourcePath(), '->__construct() set the source path');
        $this->assertEquals('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css', $asset->getSourceDirectory(), '->__construct() sets the source directory');
    }
}
