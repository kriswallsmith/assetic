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
use Assetic\Filter\EmberPrecompileFilter;

/**
 * @group integration
 */
class EmberPrecompileFilterTest extends FilterTestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        $emberBin = $this->findExecutable('ember-precompile', 'EMBERPRECOMPILE_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$emberBin) {
            $this->markTestSkipped('Unable to find `ember-precompile` executable.');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $this->asset->load();

        $this->filter = new EmberPrecompileFilter($emberBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testEmberPrecompile()
    {
        $this->filter->filterLoad($this->asset);

        $this->assertNotContains('{{ var }}', $this->asset->getContent());

        $this->assertContains('Ember.TEMPLATES["template"]', $this->asset->getContent());
        $this->assertContains('data.buffer.push("<div id=\"test\"><h2>");', $this->asset->getContent());
    }
}
