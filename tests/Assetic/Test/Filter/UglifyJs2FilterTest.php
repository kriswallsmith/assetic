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
use Assetic\Filter\UglifyJs2Filter;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group integration
 */
class UglifyJs2FilterTest extends FilterTestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        $uglifyjsBin = $this->findExecutable('uglifyjs', 'UGLIFYJS2_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');
        if (!$uglifyjsBin) {
            $this->markTestSkipped('Unable to find `uglifyjs` executable.');
        }

        // verify uglifyjs version
        $pb = new ProcessBuilder($nodeBin ? array($nodeBin, $uglifyjsBin) : array($uglifyjsBin));
        $pb->add('--version');
        if (isset($_SERVER['NODE_PATH'])) {
            $pb->setEnv('NODE_PATH', $_SERVER['NODE_PATH']);
        }
        if (0 !== $pb->getProcess()->run()) {
            $this->markTestSkipped('Incorrect version of UglifyJs');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/uglifyjs/script.js');
        $this->asset->load();

        $this->filter = new UglifyJs2Filter($uglifyjsBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testUglify()
    {
        $this->filter->filterDump($this->asset);

        $this->assertContains('function', $this->asset->getContent());
        $this->assertNotContains('/**', $this->asset->getContent());
    }

    public function testCompress()
    {
        $this->filter->setCompress(true);
        $this->filter->filterDump($this->asset);

        $this->assertContains('var var2', $this->asset->getContent());
        $this->assertNotContains('var var1', $this->asset->getContent());
    }

    public function testMangle()
    {
        $this->filter->setMangle(true);
        $this->filter->filterDump($this->asset);

        $this->assertContains('new Array(1,2,3,4)', $this->asset->getContent());
        $this->assertNotContains('var var2', $this->asset->getContent());
    }

    public function testCompressAndMangle()
    {
        $this->filter->setCompress(true);
        $this->filter->setMangle(true);
        $this->filter->filterDump($this->asset);

        $this->assertNotContains('var var1', $this->asset->getContent());
        $this->assertNotContains('var var2', $this->asset->getContent());
        $this->assertContains('new Array(1,2,3,4)', $this->asset->getContent());
    }

    public function testBeautify()
    {
        $this->filter->setBeautify(true);
        $this->filter->filterDump($this->asset);

        $this->assertContains('    foo', $this->asset->getContent());
        $this->assertNotContains('/**', $this->asset->getContent());
    }
}
