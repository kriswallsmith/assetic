<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\FileAsset;
use Assetic\Filter\JSqueezeFilter;

/**
 * @group integration
 */
class JSqueezeFilterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('JSqueeze') && !class_exists('Patchwork\JSqueeze')) {
            $this->markTestSkipped('JSqueeze is not installed.');
        }
    }

    public function testRelativeSourceUrlImportImports()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/jsmin/js.js');
        $asset->load();

        $filter = new JSqueezeFilter();
        $filter->filterDump($asset);

        $this->assertEquals(";var a='abc',bbb='u';", $asset->getContent());
    }
}
