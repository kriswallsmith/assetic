<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\DependencyExtractorInterface;
use Assetic\Asset\StringAsset;
use Assetic\Filter\CallablesFilter;
use Assetic\Factory\AssetFactory;

class CallablesFilterTest extends TestCase
{
    public function testInterface()
    {
        $filter = new CallablesFilter();
        $this->assertInstanceOf(FilterInterface::class, $filter, 'CallablesFilter implements FilterInterface');
        $this->assertInstanceOf(DependencyExtractorInterface::class, $filter, 'CallablesFilter implements DependencyExtractorInterface');
    }

    public function testLoader()
    {
        $nb = 0;
        $filter = new CallablesFilter(function ($asset) use (&$nb) { $nb++; });
        $filter->filterLoad($this->getMockBuilder(AssetInterface::class)->getMock());
        $this->assertEquals(1, $nb, '->filterLoad() calls the loader callable');
    }

    public function testDumper()
    {
        $nb = 0;
        $filter = new CallablesFilter(null, function ($asset) use (&$nb) { $nb++; });
        $filter->filterDump($this->getMockBuilder(AssetInterface::class)->getMock());
        $this->assertEquals(1, $nb, '->filterDump() calls the loader callable');
    }

    public function testDependencyExtractor()
    {
        $nb = 0;
        $self = $this;
        $assetFactoryMock = $this->getMockBuilder('Assetic\\Factory\\AssetFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $result = array(new StringAsset("test"));

        $filter = new CallablesFilter(null, null, function (AssetFactory $factory, $content, $loadPath) use (&$nb, $assetFactoryMock, $self, $result) {
            $self->assertSame($factory, $assetFactoryMock, '-> the asset factory is passed to the callable');
            $self->assertEquals('content', $content, '-> the content is passed to the callable');
            $self->assertEquals('loadPath', $loadPath, '-> the load path is passed to the callable');
            $nb++;

            return $result;
        });

        $r = $filter->getChildren($assetFactoryMock, 'content', 'loadPath');
        $this->assertEquals($result, $r, "->getChildren() returns the callable's result");
        $this->assertEquals(1, $nb, '->getChildren() calls the extractor callable');

        $filter = new CallablesFilter();
        $this->assertEquals([], $filter->getChildren($assetFactoryMock, 'ignored', 'ignored'), '-> without an extractor callable, the filter just returns an empty array (of assets)');
    }
}
