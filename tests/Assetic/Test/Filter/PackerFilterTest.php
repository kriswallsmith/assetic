<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\FileAsset;
use Assetic\Filter\PackerFilter;

/**
 * @group integration
 */
class PackerFilterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('JavaScriptPacker')) {
            $this->markTestSkipped('JavaScriptPacker is not installed.');
        }
    }

    public function testPacker()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/packer/example.js');
        $asset->load();

        $filter = new PackerFilter();
        $filter->filterDump($asset);

        $this->assertEquals("var exampleFunction=function(arg1,arg2){alert('exampleFunction called!')}", $asset->getContent());
    }
}
