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

use Assetic\Factory\AssetFactory;
use Assetic\Asset\FileAsset;
use Assetic\Filter\BrowserifyFilter;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group integration
 */
class BrowserifyFilterTest extends FilterTestCase
{
    /**
     * @var FileAsset
     */
    private $asset1;
    private $asset2;
    private $asset3;

    /**
     * @var BrowserifyFilter
     */
    private $filter;

    protected function setUp()
    {
        $browserifyBin = $this->findExecutable('browserify', 'BROWSERIFY_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');
        if (!$browserifyBin) {
            $this->markTestSkipped('Unable to find `browserify` executable.');
        }

        // verify browserify version
        $pb = new ProcessBuilder($nodeBin ? array($nodeBin, $browserifyBin) : array($browserifyBin));
        $pb->add('--version');
        if (isset($_SERVER['NODE_PATH'])) {
            $pb->setEnv('NODE_PATH', $_SERVER['NODE_PATH']);
        }
        if (0 !== $pb->getProcess()->run()) {
            $this->markTestSkipped('Incorrect version of Browserify');
        }

        $this->asset1 = new FileAsset(__DIR__.'/fixtures/browserify/script1.js');
        $this->asset2 = new FileAsset(__DIR__.'/fixtures/browserify/script2.js');
        $this->asset3 = new FileAsset(__DIR__.'/fixtures/browserify/script3.js');
        $this->asset1->load();
        $this->asset2->load();
        $this->asset3->load();

        $this->filter = new BrowserifyFilter($browserifyBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->asset1 = null;
        $this->asset2 = null;
        $this->asset3 = null;
        $this->filter = null;
    }

    public function testOutput()
    {
        $this->filter->filterDump($this->asset1);

        $this->assertContains('require,module,exports', $this->asset1->getContent());
        $this->assertContains('console.log(\'script2\')', $this->asset1->getContent());
    }

    public function testOutputWithCoreModules()
    {
        $this->filter->filterDump($this->asset3);

        $this->assertContains('http.get', $this->asset3->getContent());
    }

    public function testGetChildren()
    {
        $children = $this->filter->getChildren(new AssetFactory('/'), 'var script1 = require(\'./script1\'); var script2 = require(\'./script2\');', __DIR__.'/fixtures/browserify/');

        $this->assertCount(2, $children);
        $this->assertEquals('script2.js', $children[0]->getSourcePath());
        $this->assertEquals('script1.js', $children[1]->getSourcePath());
    }
}
