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
    private $env;

    protected function setUp()
    {
        $this->env = new Environment();
    }

    protected function tearDown()
    {
        unset($this->env);
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
}
