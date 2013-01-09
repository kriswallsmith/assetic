<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Twig;

use Assetic\Cache\ConfigCache;
use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Factory\AssetFactory;

class TwigFormulaLoaderTest extends \PHPUnit_Framework_TestCase
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
        $this->twig->addExtension(new AsseticExtension($factory, new ConfigCache(sys_get_temp_dir().'/assetic'), array(
            'some_func' => array(
                'filter' => 'some_filter',
                'options' => array('output' => 'css/*.css'),
            ),
        )));

        $this->loader = new TwigFormulaLoader($this->twig);
    }

    public function testMixture()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $expected = array(
            'mixture' => array(
                array('foo', 'foo/*', '@foo'),
                array(),
                array(
                    'output'  => 'packed/mixture',
                    'name'    => 'mixture',
                    'debug'   => false,
                    'combine' => null,
                    'vars'    => array(),
                ),
            ),
        );

        $this->twig->setLoader(new \Twig_Loader_Array(array(
            'mixture.twig' => file_get_contents(__DIR__.'/templates/mixture.twig'),
        )));

        $resource = $this->getMock('Assetic\\Factory\\Resource\\ResourceInterface');
        $resource->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('mixture.twig'));
        $this->am->expects($this->any())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));

        $formulae = $this->loader->load($resource);
        $this->assertEquals($expected, $formulae);
    }

    public function testFunction()
    {
        $expected = array(
            'my_asset' => array(
                array('path/to/asset'),
                array('some_filter'),
                array('output' => 'css/*.css', 'name' => 'my_asset'),
            ),
        );

        $this->twig->setLoader(new \Twig_Loader_Array(array(
            'function.twig' => file_get_contents(__DIR__.'/templates/function.twig'),
        )));

        $resource = $this->getMock('Assetic\\Factory\\Resource\\ResourceInterface');
        $resource->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('function.twig'));

        $formulae = $this->loader->load($resource);
        $this->assertEquals($expected, $formulae);
    }
}
