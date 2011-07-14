<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\TwigFilter;

/**
 * @group integration
 */
class TwigFilterTest extends \PHPUnit_Framework_TestCase
{
    private $twig;
    private $filter;

    protected function setUp()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not installed.');
        }

        $this->loader = $this->getMock('Twig_LoaderInterface');
        $this->loader->expects($this->any())
            ->method('getCacheKey')
            ->will($this->returnValue(sha1(rand(11111, 99999))));

        $this->twig = new \Twig_Environment($this->loader);
        $this->filter = new TwigFilter($this->twig);
    }

    public function testFilterLoad()
    {
        $asset = new StringAsset('{{ "foobar"|upper ~ "baz" }}');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals('FOOBARbaz', $asset->getContent(), '->filterLoad() parses the asset as a Twig template');
    }

    public function testContext()
    {
        $asset = new StringAsset('{{ foobar }}');
        $asset->load();

        $this->filter->addContextValue('foobar', 'ok');
        $this->filter->filterLoad($asset);

        $this->assertEquals('ok', $asset->getContent(), '->filterLoad() includes context values from the filter');
    }
}
