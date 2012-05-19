<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Css\Processor;

use Assetic\Asset\Asset;
use Assetic\Extension\Css\Processor\CssChildrenProcessor;

class CssChildrenProcessorTest extends \PHPUnit_Framework_TestCase
{
    private $factory;
    private $processor;

    protected function setUp()
    {
        $this->factory = $this->getMock('Assetic\Asset\FactoryInterface');
        $this->processor = new CssChildrenProcessor($this->factory);
    }

    protected function tearDown()
    {
        unset($this->factory, $this->processor);
    }

    /**
     * @test
     */
    public function shouldLoadImports()
    {
        $asset = new Asset(array(
            'content' => "@import 'fonts.css';\nbody { font-size: 10px; }\n",
            'extensions'   => array('css'),
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

        $this->processor->process($asset);
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
            'extensions'   => array('css'),
        ));

        $this->factory->expects($this->never())
            ->method('createAsset');

        $this->processor->process($asset);
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
            'extensions'   => array('css'),
        ));

        $log = array();
        $test = $this;

        $this->factory->expects($this->any())
            ->method('createAsset')
            ->will($this->returnCallback(function($attr) use(&$log, $test) {
                $log[$attr['parent.rev_path']][] = isset($attr['parent.line']) ? $attr['parent.line'] : null;
                return $test->getMock('Assetic\Asset\AssetInterface');
            }));

        $this->processor->process($asset);

        $this->assertCount(4, $asset->getChildren());
        $this->assertEquals(array(
            'fonts.css'          => array(1),
            '../images/bg.gif'   => array(3, 9),
            '../images/logo.gif' => array(6),
        ), $log);
    }
}
