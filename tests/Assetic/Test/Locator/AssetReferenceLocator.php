<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Locator;

use Assetic\Locator\AssetReferenceLocator;

class AssetReferenceLocatorTest extends \PHPUnit_Framework_TestCase
{
    private $locator;

    protected function setUp()
    {
        $am = $this->getMockBuilder('Assetic\\AssetManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->locator = new AssetReferenceLocator($am);
    }

    public function testCorrectInput()
    {
        $asset = $this->locator->locate('@jquery');

        $this->assertNotNull($asset);
        $this->assertInstanceOf('Assetic\\Asset\\AssetReference', $asset);
    }

    public function testWrongInput()
    {
        $asset = $this->locator->locate('jquery');

        $this->assertNull($asset);
    }
}
