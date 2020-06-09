<?php namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\HandlebarsFilter;

/**
 * @group integration
 */
class HandlebarsFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp(): void
    {
        $handlebarsBin = $this->findExecutable('handlebars', 'HANDLEBARS_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$handlebarsBin) {
            $this->markTestSkipped('Unable to find `handlebars` executable.');
        }

        $this->filter = new HandlebarsFilter($handlebarsBin, $nodeBin);
    }

    protected function tearDown(): void
    {
        $this->filter = null;
    }

    public function testHandlebars()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertStringNotContainsString('{{ var }}', $asset->getContent());
        $this->assertStringContainsString('(function() {', $asset->getContent());
    }

    public function testSimpleHandlebars()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $asset->load();

        $this->filter->setSimple(true);
        $this->filter->filterLoad($asset);

        $this->assertStringNotContainsString('{{ var }}', $asset->getContent());
        $this->assertStringNotContainsString('(function() {', $asset->getContent());
    }

    public function testMinimizeHandlebars()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $asset->load();

        $this->filter->setMinimize(true);
        $this->filter->filterLoad($asset);

        $this->assertStringNotContainsString('{{ var }}', $asset->getContent());
        $this->assertStringNotContainsString("\n", $asset->getContent());
    }

    public function testStringAssset()
    {
        $asset = new StringAsset(file_get_contents(__DIR__.'/fixtures/handlebars/template.handlebars'));
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertStringNotContainsString('{{ var }}', $asset->getContent());
        $this->assertStringContainsString('(function() {', $asset->getContent());
    }
}
