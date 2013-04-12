<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\HandlebarsFilter;

/**
 * @group integration
 */
class HandlebarsFilterTest extends FilterTestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        $handlebarsBin = $this->findExecutable('handlebars', 'HANDLEBARS_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$handlebarsBin) {
            $this->markTestSkipped('Unable to find `handlebars` executable.');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $this->asset->load();

        $this->filter = new HandlebarsFilter($handlebarsBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testHandlebars()
    {
        $this->filter->filterLoad($this->asset);

        $this->assertNotContains('{{ var }}', $this->asset->getContent());
        $this->assertContains('(function() {', $this->asset->getContent());
    }

    public function testSimpleHandlebars()
    {
        $this->filter->setSimple(true);
        $this->filter->filterLoad($this->asset);

        $this->assertNotContains('{{ var }}', $this->asset->getContent());
        $this->assertNotContains('(function() {', $this->asset->getContent());
    }

    public function testMinimizeHandlebars()
    {
        $this->filter->setMinimize(true);
        $this->filter->filterLoad($this->asset);

        $this->assertNotContains('{{ var }}', $this->asset->getContent());
        $this->assertNotContains("\n", $this->asset->getContent());
    }
}
