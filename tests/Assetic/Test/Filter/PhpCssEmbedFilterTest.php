<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\FileAsset;
use Assetic\Filter\PhpCssEmbedFilter;

/**
 * @group integration
 */
class PhpCssEmbedFilterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('CssEmbed\CssEmbed')) {
            $this->markTestSkipped('PhpCssEmbed is not installed');
        }
    }

    public function testCssEmbedDataUri()
    {
        $data = base64_encode(file_get_contents(__DIR__.'/fixtures/home.png'));

        $asset = new FileAsset(__DIR__.'/fixtures/cssembed/test.css');
        $asset->load();

        $filter = new PhpCssEmbedFilter();
        $filter->filterLoad($asset);

        $this->assertStringContainsString('url(data:image/png;base64,'.$data, $asset->getContent());
    }
}
