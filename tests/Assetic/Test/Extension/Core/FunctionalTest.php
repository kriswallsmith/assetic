<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Core;

use Assetic\Asset\Asset;
use Assetic\Environment;
use Assetic\Extension\Core\CoreExtension;
use Assetic\Tree\CallableVisitor;

/**
 * @group functional
 */
class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    private $env;

    protected function setUp()
    {
        $this->env = new Environment();
        $this->env->addExtension(new CoreExtension(array(
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
                'extensions'   => array('css'),
            )),
            array('css/more', array(
                'content' => '/* path2/css/more.sass */',
                'extensions'   => array('sass'),
            )),
        );
    }

    /**
     * @test
     */
    public function shouldLoadAddedChildren()
    {
        $this->env->getLoader()->addVisitor(new CallableVisitor(function($asset) {
            if ('css/bg' == $asset->getAttribute('logical_path') && !$asset->getChildren()) {
                $asset->addChildren(array(new Asset(array('parent.rev_path' => '../images/bg.gif'))));
            }

            return $asset;
        }));

        $asset = $this->env->load('css/bg');
        $children = $asset->getChildren();

        $this->assertCount(1, $children);
        $this->assertEquals('css/../images/bg.gif', $children[0]->getAttribute('logical_path'));
    }
}
