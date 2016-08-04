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
use Assetic\Filter\NodeSassFilter;

/**
 * @group integration
 */
class NodeSassFilterTest extends FilterTestCase
{
    private $asset;

    /**
     * @var NodeSassFilter
     */
    private $filter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $nodeSassBin = $this->findExecutable('node-sass', 'NODESASS_BIN');
        if (!$nodeSassBin) {
            $this->markTestSkipped('Unable to find `node-sass` executable.');
        }

        $this->filter = new NodeSassFilter($nodeSassBin);
        $this->filter->setOutputStyle('compressed');
        $this->filter->setLinefeed('lf');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    /**
     * @group integration
     */
    public function testSassCompilation()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/nodesass/main.sass');
        $asset->load();

        $this->filter->filterDump($asset);

        $this->assertEquals(".foo{color:red}\n", $asset->getContent());
    }

    /**
     * @group integration
     */
    public function testScssCompilation()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/nodesass/main.scss');
        $asset->load();

        $this->filter->filterDump($asset);

        $this->assertEquals(".foo{color:red}\n", $asset->getContent());
    }

    /**
     * @group integration
     */
    public function testIncludePaths()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/nodesass/import.scss');
        $asset->load();

        $this->filter->addIncludePath(__DIR__.'/fixtures/nodesass/includes');
        $this->filter->filterDump($asset);

        $this->assertEquals(".foo{color:green}.bar{color:red}\n", $asset->getContent());
    }
}
