<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Twig;

use Assetic\Factory\AssetFactory;
use Assetic\Extension\Twig\AsseticExtension;

class AsseticExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $am;
    private $fm;
    private $twig;

    protected function setUp()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not installed.');
        }

        $this->am = $this->getMock('Assetic\\AssetManager');
        $this->fm = $this->getMock('Assetic\\FilterManager');

        $factory = new AssetFactory(__DIR__.'/templates');
        $factory->setAssetManager($this->am);
        $factory->setFilterManager($this->fm);

        $this->twig = new \Twig_Environment();
        $this->twig->setLoader(new \Twig_Loader_Filesystem(__DIR__.'/templates'));
        $this->twig->addExtension(new AsseticExtension($factory));
    }

    public function testReference()
    {
        $xml = $this->renderXml('reference.twig');
        $this->assertEquals(1, count($xml->asset));
        $this->assertStringStartsWith('assets/', (string) $xml->asset['url']);
    }

    public function testGlob()
    {
        $xml = $this->renderXml('glob.twig');
        $this->assertEquals(1, count($xml->asset));
        $this->assertStringStartsWith('assets/', (string) $xml->asset['url']);
    }

    public function testAbsolutePath()
    {
        $xml = $this->renderXml('absolute_path.twig');
        $this->assertEquals(1, count($xml->asset));
        $this->assertStringStartsWith('assets/', (string) $xml->asset['url']);
    }

    public function testFilters()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');

        $this->fm->expects($this->at(0))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($filter));
        $this->fm->expects($this->at(1))
            ->method('get')
            ->with('bar')
            ->will($this->returnValue($filter));

        $xml = $this->renderXml('filters.twig');
        $this->assertEquals(1, count($xml->asset));
        $this->assertStringStartsWith('assets/', (string) $xml->asset['url']);
    }

    public function testOptionalFilter()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');

        $this->fm->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($filter));

        $xml = $this->renderXml('optional_filter.twig');
        $this->assertEquals(1, count($xml->asset));
        $this->assertStringStartsWith('assets/', (string) $xml->asset['url']);
    }

    public function testOutputPattern()
    {
        $xml = $this->renderXml('output_pattern.twig');
        $this->assertEquals(1, count($xml->asset));
        $this->assertStringStartsWith('css/packed/', (string) $xml->asset['url']);
        $this->assertStringEndsWith('.css', (string) $xml->asset['url']);
    }

    public function testOutput()
    {
        $xml = $this->renderXml('output_url.twig');
        $this->assertEquals(1, count($xml->asset));
        $this->assertEquals('explicit_url.css', (string) $xml->asset['url']);
    }

    public function testMixture()
    {
        $xml = $this->renderXml('mixture.twig');
        $this->assertEquals(1, count($xml->asset));
        $this->assertEquals('packed/mixture', (string) $xml->asset['url']);
    }

    public function testDebug()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');

        $this->fm->expects($this->once())
            ->method('get')
            ->with('bar')
            ->will($this->returnValue($filter));

        $xml = $this->renderXml('debug.twig');
        $this->assertEquals(2, count($xml->asset));
        $this->assertStringStartsWith('css/packed_', (string) $xml->asset[0]['url']);
        $this->assertStringEndsWith('.css', (string) $xml->asset[0]['url']);
    }

    private function renderXml($name, $context = array())
    {
        return new \SimpleXMLElement($this->twig->loadTemplate($name)->render($context));
    }
}
