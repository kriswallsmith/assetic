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
use Assetic\Filter\EmberPrecompileFilter;

/**
 * @group integration
 */
class EmberPrecompileFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        $emberBin = $this->findExecutable('ember-precompile', 'EMBERPRECOMPILE_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$emberBin) {
            $this->markTestSkipped('Unable to find `ember-precompile` executable.');
        }

        $this->filter = new EmberPrecompileFilter($emberBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->filter = null;
    }

    public function testFileAsset()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertNotContains('{{ var }}', $asset->getContent());
        $this->assertContains('Ember.TEMPLATES["template"]', $asset->getContent());
        $this->assertContains('data.buffer.push("<div id=\"test\"><h2>");', $asset->getContent());
    }

    /**
     * @expectedException \LogicException
     */
    public function testStringAsset()
    {
        $asset = new StringAsset(file_get_contents(__DIR__.'/fixtures/handlebars/template.handlebars'));
        $asset->load();

        $this->filter->filterLoad($asset);
    }
}
