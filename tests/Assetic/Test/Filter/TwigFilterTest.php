<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\TwigFilter;

/**
 * @group integration
 */
class TwigFilterTest extends \PHPUnit_Framework_TestCase
{
    private $filter;

    protected function setUp()
    {
        if (!isset($_SERVER['NODE_BIN']) || !isset($_SERVER['NODE_PATH'])) {
            $this->markTestSkipped('No node.js configuration.');
        }

        $this->filter = new TwigFilter($_SERVER['NODE_BIN'], array($_SERVER['NODE_PATH']));
    }

    public function testFilterLoad()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/twig/template.twig');
        $asset->load();

        $this->filter->filterLoad($asset);

        $compiled = $this->getCompiledTestTemplate('template.twig');
        $this->assertEquals($compiled, $asset->getContent());
    }

    public function testFilterLoadWithTemplateSubfolder()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/twig/subfolder/template.twig', array(), __DIR__.'/fixtures/twig');
        $asset->load();

        $this->filter->filterLoad($asset);

        $compiled = $this->getCompiledTestTemplate('subfolder/template.twig');
        $this->assertEquals($compiled, $asset->getContent());
    }

    public function getCompiledTestTemplate($name)
    {
        $template = 'twig({id:"%s", data:[{"type":"raw","value":"Hello, "},'
                  . '{"type":"output","stack":[{"type":"Twig.expression.type.variable",'
                  . '"value":"name","match":["name"]}]},{"type":"raw","value":"!\n"}],'
                  . ' precompiled: true});';

        return sprintf($template, $name);
    }
}
