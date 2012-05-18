<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Loader;

use Assetic\Environment;
use Assetic\Extension\Loader\LoaderExtension;

/**
 * @group functional
 */
class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    private $env;

    protected function setUp()
    {
        $this->env = new Environment();
        $this->env->addExtension(new LoaderExtension(array(
            __DIR__.'/Fixtures/path1',
            __DIR__.'/Fixtures/path2',
        )));
    }

    protected function tearDown()
    {
        unset($this->env);
    }

    /**
     * @test
     * @dataProvider provideLogicalPaths
     */
    public function shouldLoadAssetFromSource($logicalPath, $expectedAttributes)
    {
        $asset = $this->env->load($logicalPath);

        foreach ($expectedAttributes as $name => $value) {
            $this->assertEquals($value, $asset->getAttribute($name));
        }
    }

    public function provideLogicalPaths()
    {
        return array(
            array('css/core', array(
                'content' => '/* path1/css/core.css */',
                'types'   => array('css'),
            )),
            array('css/more', array(
                'content' => '/* path2/css/more.sass */',
                'types'   => array('sass'),
            )),
        );
    }
}
