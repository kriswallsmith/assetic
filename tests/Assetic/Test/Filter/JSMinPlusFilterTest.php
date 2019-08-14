<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\FileAsset;
use Assetic\Filter\JSMinPlusFilter;

/**
 * @group integration
 */
class JSMinPlusFilterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('JSMinPlus')) {
            $this->markTestSkipped('JSMinPlus is not installed.');
        }
    }

    public function testRelativeSourceUrlImportImports()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/jsmin/js.js');
        $asset->load();

        $filter = new JSMinPlusFilter();
        $filter->filterDump($asset);

        $this->assertEquals('var a="abc",bbb="u"', $asset->getContent());
    }
}
