<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\FileAsset;
use Assetic\Filter\CssMinFilter;

/**
 * @group integration
 */
class CssMinFilterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('CssMin')) {
            $this->markTestSkipped('CssMin is not installed.');
        }
    }

    public function testRelativeSourceUrlImportImports()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/cssmin/main.css');
        $asset->load();

        $filter = new CssMinFilter(__DIR__.'/fixtures/cssmin');
        $filter->setFilter('ImportImports', true);
        $filter->filterDump($asset);

        $this->assertEquals('body{color:white}body{background:black}', $asset->getContent());
    }
}
