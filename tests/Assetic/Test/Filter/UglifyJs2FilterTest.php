<?php namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\UglifyJs2Filter;
use Symfony\Component\Process\Process;

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

    protected function setUp(): void
    {
        $uglifyjsBin = $this->findExecutable('uglifyjs', 'UGLIFYJS2_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');
        if (!$uglifyjsBin) {
            $this->markTestSkipped('Unable to find `uglifyjs` executable.');
        }

        // verify uglifyjs version
        $pb = new Process(array_merge($nodeBin ? array($nodeBin, $uglifyjsBin) : array($uglifyjsBin), ['--version']));
        if (isset($_SERVER['NODE_PATH'])) {
            $pb->setEnv(['NODE_PATH' => $_SERVER['NODE_PATH']]);
        }
        if (0 !== $pb->run()) {
            $this->markTestSkipped('Incorrect version of UglifyJs');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/uglifyjs/script.js');
        $this->asset->load();

        $this->filter = new UglifyJs2Filter($uglifyjsBin, $nodeBin);
    }

    protected function tearDown(): void
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testDefines()
    {
        $this->filter->setDefines(array('DEBUG=false'));
        $this->filter->filterDump($this->asset);

        $this->assertStringContainsString('FOO', $this->asset->getContent());
        $this->assertStringNotContainsString('console.log', $this->asset->getContent());
    }

    public function testMutiplieDefines()
    {
        $this->filter->setDefines(array('DEBUG=false', 'FOO=2'));
        $this->filter->filterDump($this->asset);

        $this->assertStringNotContainsString('DEBUG', $this->asset->getContent());
        $this->assertStringNotContainsString('FOO', $this->asset->getContent());
        $this->assertStringContainsString('Array(2,2,3,4)', $this->asset->getContent());
        $this->assertStringNotContainsString('console.log', $this->asset->getContent());
    }

    public function testUglify()
    {
        $this->filter->filterDump($this->asset);

        $this->assertStringContainsString('function', $this->asset->getContent());
        $this->assertStringNotContainsString('/**', $this->asset->getContent());
    }

    public function testCompress()
    {
        $this->filter->setCompress(true);
        $this->filter->filterDump($this->asset);

        $this->assertStringContainsString('var bar', $this->asset->getContent());
        $this->assertStringNotContainsString('var var1', $this->asset->getContent());
    }

    public function testCompressOptions()
    {
        $this->filter->setCompress('drop_console');
        $this->filter->filterDump($this->asset);

        $this->assertStringNotContainsString('console.log', $this->asset->getContent());
    }

    public function testMangle()
    {
        $this->filter->setMangle(true);
        $this->filter->filterDump($this->asset);

        $this->assertStringContainsString('new Array(FOO,2,3,4)', $this->asset->getContent());
        $this->assertStringNotContainsString('var var2', $this->asset->getContent());
    }

    public function testCompressAndMangle()
    {
        $this->filter->setCompress(true);
        $this->filter->setMangle(true);
        $this->filter->filterDump($this->asset);

        $this->assertStringNotContainsString('var var1', $this->asset->getContent());
        $this->assertStringNotContainsString('var var2', $this->asset->getContent());
        $this->assertStringContainsString('Array(FOO,2,3,4)', $this->asset->getContent());
    }

    public function testDefinesAndCompress()
    {
        $this->filter->setCompress(true);
        $this->filter->setDefines(array('DEBUG=false'));
        $this->filter->filterDump($this->asset);

        $this->assertStringNotContainsString('DEBUG', $this->asset->getContent());
        $this->assertStringNotContainsString('console.log', $this->asset->getContent());
    }

    public function testMutipleDefines()
    {
        $this->filter->setCompress(true);
        $this->filter->setDefines(array('DEBUG=false', 'FOO=2'));
        $this->filter->filterDump($this->asset);

        $this->assertStringNotContainsString('DEBUG', $this->asset->getContent());
        $this->assertStringNotContainsString('FOO', $this->asset->getContent());
        $this->assertStringContainsString('Array(2,2,3,4)', $this->asset->getContent());
        $this->assertStringNotContainsString('console.log', $this->asset->getContent());
    }

    public function testBeautify()
    {
        $this->filter->setBeautify(true);
        $this->filter->filterDump($this->asset);

        $this->assertStringContainsString('    foo', $this->asset->getContent());
        $this->assertStringNotContainsString('/**', $this->asset->getContent());
    }
}
