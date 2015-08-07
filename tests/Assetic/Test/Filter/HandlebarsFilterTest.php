<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\HandlebarsFilter;

/**
 * @group integration
 */
class HandlebarsFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        $handlebarsBin = $this->findExecutable('handlebars', 'HANDLEBARS_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$handlebarsBin) {
            $this->markTestSkipped('Unable to find `handlebars` executable.');
        }

        $this->filter = new HandlebarsFilter($handlebarsBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->filter = null;
    }

    public function testHandlebars()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertNotContains('{{ var }}', $asset->getContent());
        $this->assertContains('(function() {', $asset->getContent());
    }

    public function testSimpleHandlebars()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $asset->load();

        $this->filter->setSimple(true);
        $this->filter->filterLoad($asset);

        $this->assertNotContains('{{ var }}', $asset->getContent());
        $this->assertNotContains('(function() {', $asset->getContent());
    }

    public function testMinimizeHandlebars()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $asset->load();

        $this->filter->setMinimize(true);
        $this->filter->filterLoad($asset);

        $this->assertNotContains('{{ var }}', $asset->getContent());
        $this->assertNotContains("\n", $asset->getContent());
    }

    /**
     * @expectedException \LogicException
     */
    public function testStringAssset()
    {
        $asset = new StringAsset(file_get_contents(__DIR__.'/fixtures/handlebars/template.handlebars'));
        $asset->load();

        $this->filter->filterLoad($asset);
    }
}
