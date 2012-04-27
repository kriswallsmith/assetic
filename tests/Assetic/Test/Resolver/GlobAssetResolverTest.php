<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Resolver;

use Assetic\Resolver\GlobAssetResolver;

class GlobAssetResolverTest extends \PHPUnit_Framework_TestCase
{
    private $locator;

    protected function setUp()
    {
        $this->locator = new GlobAssetResolver(__DIR__.'/../Fixture/root');
    }

    public function testCorrectInput()
    {
        $asset = $this->locator->resolve('*.js', array('vars' => array()));

        $this->assertNotNull($asset);
        $this->assertInstanceOf('Assetic\\Asset\\GlobAsset', $asset);
    }

    public function testWrongInput()
    {
        $asset = $this->locator->resolve('js/jquery.js', array('vars' => array()));

        $this->assertNull($asset);
    }
}
