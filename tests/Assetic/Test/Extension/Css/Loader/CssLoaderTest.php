<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Css\Loader;

use Assetic\Asset\Asset;
use Assetic\Extension\Css\Loader\CssLoader;

class CssLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $factory;
    private $loader;

    protected function setUp()
    {
        $this->factory = $this->getMock('Assetic\Asset\FactoryInterface');
        $this->loader = new CssLoader($this->factory);
    }

    protected function tearDown()
    {
        unset($this->factory, $this->loader);
    }

    /**
     * @test
     * @dataProvider provideContentAndTypes
     */
    public function shouldReturnAsset($content, $types)
    {
        $asset = new Asset(array('content' => $content, 'types' => $types));
        $this->assertInstanceOf('Assetic\Asset\AssetInterface', $this->loader->enter($asset));
    }

    public function provideContentAndTypes()
    {
        return array(
            array('asdf', array('css')),
            array('asdf', array('txt')),
            array(null, array('css')),
            array(null, array('txt')),
        );
    }

    /**
     * @test
     */
    public function shouldLoadImports()
    {
        $asset = new Asset(array(
            'content' => "@import 'fonts.css';\nbody { font-size: 10px; }\n",
            'types'   => array('css'),
        ));

        $child = $this->getMock('Assetic\Asset\AssetInterface');

        $this->factory->expects($this->once())
            ->method('createAsset')
            ->with(array(
                'parent.rev_path' => 'fonts.css',
                'parent.fragment' => '@import \'fonts.css\';',
                'parent.line'     => 1,
            ))
            ->will($this->returnValue($child));

        $this->assertSame($asset, $this->loader->enter($asset));
        $this->assertCount(1, $asset->getChildren());
    }

    /**
     * @test
     * @dataProvider provideCommentContent
     */
    public function shouldIgnoreComments($content)
    {
        $asset = new Asset(array(
            'content' => $content,
            'types'   => array('css'),
        ));

        $this->factory->expects($this->never())
            ->method('createAsset');

        $this->loader->enter($asset);
    }

    public function provideCommentContent()
    {
        return array(
            array('/* @import "fonts.css"; */'),
            array("/*\n@import 'fonts.css';\n*/"),
        );
    }

    /**
     * @test
     */
    public function shouldDetectLineNumber()
    {
        $content = <<<CSS
@import 'fonts.css';
body {
    background-image: url("../images/bg.gif");
}
#logo {
    background-image: url("../images/logo.gif");
}
.footer {
    background-image: url("../images/bg.gif");
}
CSS;

        $asset = new Asset(array(
            'content' => $content,
            'types'   => array('css'),
        ));

        $log = array();
        $test = $this;

        $this->factory->expects($this->any())
            ->method('createAsset')
            ->will($this->returnCallback(function($attr) use(&$log, $test) {
                $log[$attr['parent.rev_path']][] = isset($attr['parent.line']) ? $attr['parent.line'] : null;
                return $test->getMock('Assetic\Asset\AssetInterface');
            }));

        $asset = $this->loader->enter($asset);

        $this->assertCount(4, $asset->getChildren());
        $this->assertEquals(array(
            'fonts.css'          => array(1),
            '../images/bg.gif'   => array(3, 9),
            '../images/logo.gif' => array(6),
        ), $log);
    }
}
