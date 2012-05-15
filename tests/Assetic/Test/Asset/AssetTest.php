<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\Asset;

class AssetTest extends \PHPUnit_Framework_TestCase
{
    private $asset;

    protected function setUp()
    {
        $this->asset = new Asset();
    }

    protected function tearDown()
    {
        unset($this->asset);
    }

    public function testNothing()
    {
    }
}
