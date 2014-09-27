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
use Assetic\Filter\UglifyJs2Filter;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group integration
 */
class UglifyJs2FilterTest extends FilterTestCase
{
    /**
     * @var FileAsset
     */
    private $asset;

    /**
     * @var UglifyJs2Filter
     */
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

    public function testDefines()
    {
        $this->filter->setDefines(array('DEBUG=false'));
        $this->filter->filterDump($this->asset);

        $this->assertContains('DEBUG', $this->asset->getContent());
        $this->assertContains('console.log', $this->asset->getContent());
    }

    public function testMutiplieDefines()
    {
        $this->filter->setDefines(array('DEBUG=false', 'FOO=2'));
        $this->filter->filterDump($this->asset);

        $this->assertContains('DEBUG', $this->asset->getContent());
        $this->assertContains('FOO', $this->asset->getContent());
        $this->assertContains('Array(FOO,2,3,4)', $this->asset->getContent());
        $this->assertContains('console.log', $this->asset->getContent());
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

        $this->assertContains('var foo', $this->asset->getContent());
        $this->assertNotContains('var var1', $this->asset->getContent());
    }

    public function testCompressOptions()
    {
        $this->filter->setCompress('drop_console');
        $this->filter->filterDump($this->asset);

        $this->assertNotContains('console.log', $this->asset->getContent());
    }

    public function testMangle()
    {
        $this->filter->setMangle(true);
        $this->filter->filterDump($this->asset);

        $this->assertContains('new Array(FOO,2,3,4)', $this->asset->getContent());
        $this->assertNotContains('var var2', $this->asset->getContent());
    }

    public function testCompressAndMangle()
    {
        $this->filter->setCompress(true);
        $this->filter->setMangle(true);
        $this->filter->filterDump($this->asset);

        $this->assertNotContains('var var1', $this->asset->getContent());
        $this->assertNotContains('var var2', $this->asset->getContent());
        $this->assertContains('Array(FOO,2,3,4)', $this->asset->getContent());
    }

    public function testDefinesAndCompress()
    {
        $this->filter->setCompress(true);
        $this->filter->setDefines(array('DEBUG=false'));
        $this->filter->filterDump($this->asset);

        $this->assertNotContains('DEBUG', $this->asset->getContent());
        $this->assertNotContains('console.log', $this->asset->getContent());
    }

    public function testMutipleDefines()
    {
        $this->filter->setCompress(true);
        $this->filter->setDefines(array('DEBUG=false', 'FOO=2'));
        $this->filter->filterDump($this->asset);

        $this->assertNotContains('DEBUG', $this->asset->getContent());
        $this->assertNotContains('FOO', $this->asset->getContent());
        $this->assertContains('Array(2,2,3,4)', $this->asset->getContent());
        $this->assertNotContains('console.log', $this->asset->getContent());
    }

    public function testBeautify()
    {
        $this->filter->setBeautify(true);
        $this->filter->filterDump($this->asset);

        $this->assertContains('    foo', $this->asset->getContent());
        $this->assertNotContains('/**', $this->asset->getContent());
    }
}
