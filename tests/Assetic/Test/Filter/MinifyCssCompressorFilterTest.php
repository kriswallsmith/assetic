<?php namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\MinifyCssCompressorFilter;

/**
 * @group integration
 */
class MinifyCssCompressorFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
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
