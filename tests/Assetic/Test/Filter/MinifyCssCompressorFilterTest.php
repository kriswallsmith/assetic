<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\FileAsset;
use Assetic\Filter\MinifyCssCompressorFilter;

/**
 * @group integration
 */
class MinifyCssCompressorFilterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('Minify_CSS_Compressor')) {
            $this->markTestSkipped('MinifyCssCompressor is not installed.');
        }
    }

    public function testRelativeSourceUrlImportImports()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/minifycsscompressor/main.css');
        $asset->load();

        $filter = new MinifyCssCompressorFilter();
        $filter->filterDump($asset);

        $this->assertEquals('body{color:white}body{background:black}', $asset->getContent());
    }
}
