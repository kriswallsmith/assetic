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

use Assetic\Locator\GlobAssetLocator;

class GlobAssetLocatorTest extends \PHPUnit_Framework_TestCase
{
    private $locator;

    protected function setUp()
    {
        $this->locator = new GlobAssetLocator(__DIR__.'/../Fixture/root');
    }

    public function testCorrectInput()
    {
        $asset = $this->locator->locate('*.js', array('vars' => array()));

        $this->assertNotNull($asset);
        $this->assertInstanceOf('Assetic\\Asset\\GlobAsset', $asset);
    }

    public function testWrongInput()
    {
        $asset = $this->locator->locate('js/jquery.js', array('vars' => array()));

        $this->assertNull($asset);
    }
}
