<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Twig;

use Assetic\Extension\Twig\TwigResource;

class TwigResourceTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not installed.');
        }
    }

    public function testInvalidTemplateNameGetContent()
    {
        if (method_exists('Twig_LoaderInterface', 'getSourceContext')) {
            $loaderType = 'Twig_LoaderInterface';
        } else {
            $loaderType = array('Twig_LoaderInterface', 'Twig_SourceContextLoaderInterface');
        }

        $loader = $this->getMock($loaderType);
        $loader->expects($this->once())
            ->method('getSourceContext')
            ->with('asdf')
            ->will($this->throwException(new \Twig_Error_Loader('')));

        $resource = new TwigResource($loader, 'asdf');
        $this->assertEquals('', $resource->getContent());
    }

    /**
     * @group legacy
     */
    public function testInvalidTemplateNameGetContentWithLegacyLoader()
    {
        if (!method_exists('Twig_LoaderInterface', 'getSource')) {
            $this->markTestSkipped('This test does not make sense on Twig 2.x.');
        }

        $loader = $this->getMock('Twig_LoaderInterface');
        $loader->expects($this->once())
            ->method('getSource')
            ->with('asdf')
            ->will($this->throwException(new \Twig_Error_Loader('')));

        $resource = new TwigResource($loader, 'asdf');
        $this->assertEquals('', $resource->getContent());
    }

    public function testInvalidTemplateNameIsFresh()
    {
        $loader = $this->getMock('Twig_LoaderInterface');
        $loader->expects($this->once())
            ->method('isFresh')
            ->with('asdf', 1234)
            ->will($this->throwException(new \Twig_Error_Loader('')));

        $resource = new TwigResource($loader, 'asdf');
        $this->assertFalse($resource->isFresh(1234));
    }
}
