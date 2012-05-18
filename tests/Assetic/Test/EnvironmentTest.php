<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test;

use Assetic\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    private $factory;
    private $env;

    protected function setUp()
    {
        $this->factory = $this->getMock('Assetic\Asset\FactoryInterface');
        $this->env = new Environment($this->factory);
    }

    protected function tearDown()
    {
        unset($this->factory, $this->env);
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function shouldErrorOnLateExtension()
    {
        $extension = $this->getMock('Assetic\ExtensionInterface');

        $this->env->initialize();
        $this->env->addExtension($extension);
    }

    /**
     * @test
     */
    public function shouldInitializeExtensions()
    {
        $extension = $this->getMock('Assetic\ExtensionInterface');

        $extension->expects($this->any())
            ->method('getLoaderVisitors')
            ->will($this->returnValue(array()));
        $extension->expects($this->any())
            ->method('getProcessorVisitors')
            ->will($this->returnValue(array()));
        $extension->expects($this->once())
            ->method('initialize');

        $this->env->addExtension($extension);
        $this->env->initialize();
    }

    /**
     * @test
     * @dataProvider provideTraverserMethods
     */
    public function shouldInitializeTraversers($method)
    {
        $this->assertInstanceOf('Assetic\Tree\TraverserInterface', $this->env->$method());
    }

    public function provideTraverserMethods()
    {
        return array(
            array('getLoader'),
            array('getProcessor'),
        );
    }

    /**
     * @test
     */
    public function shouldAddLoaderVisitors()
    {
        $extension = $this->getMock('Assetic\ExtensionInterface');
        $visitor = $this->getMock('Assetic\Tree\VisitorInterface');
        $node = $this->getMock('Assetic\Tree\NodeInterface');

        $extension->expects($this->once())
            ->method('getLoaderVisitors')
            ->will($this->returnValue(array($visitor)));
        $extension->expects($this->once())
            ->method('getProcessorVisitors')
            ->will($this->returnValue(array()));
        $visitor->expects($this->once())
            ->method('enter')
            ->will($this->returnValue($node));
        $visitor->expects($this->once())
            ->method('leave')
            ->will($this->returnValue($node));
        $node->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array()));

        $this->env->addExtension($extension);
        $loader = $this->env->getLoader();
        $loader->traverse($node);
    }

    /**
     * @test
     */
    public function shouldLoadAsset()
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');

        $this->factory->expects($this->once())
            ->method('createAsset')
            ->with(array('logical_path' => 'foo/bar'))
            ->will($this->returnValue($asset));
        $asset->expects($this->any())
            ->method('getAttributes')
            ->will($this->returnValue(array()));
        $asset->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array()));

        $this->assertSame($asset, $this->env->load('foo/bar'));
    }

    /**
     * @test
     */
    public function shouldReturnFactory()
    {
        $this->assertSame($this->factory, $this->env->getFactory());
    }

    /**
     * @test
     */
    public function shouldCreateFactory()
    {
        $env = new Environment();
        $this->assertInstanceOf('Assetic\Asset\FactoryInterface', $env->getFactory());
    }
}
