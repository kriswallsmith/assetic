<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\FileAsset;
use Assetic\Filter\JSMinFilter;

/**
 * @group integration
 */
class JSMinFilterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('JSMin')) {
            $this->markTestSkipped('JSMin is not installed.');
        }
    }

    public function testRelativeSourceUrlImportImports()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/jsmin/js.js');
        $asset->load();

        $filter = new JSMinFilter();
        $filter->filterDump($asset);

        $this->assertEquals('var a="abc";;;var bbb="u";', $asset->getContent());
    }
}
