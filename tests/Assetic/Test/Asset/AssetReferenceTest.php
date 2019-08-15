<?php namespace Assetic\Test\Asset;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Asset\AssetReference;
use Assetic\Asset\StringAsset;
use Assetic\AssetManager;

class AssetReferenceTest extends TestCase
{
    private $am;
    private $ref;

    protected function setUp(): void
    {
        $this->am = $this->getMockBuilder(AssetManager::class)->getMock();
        $this->ref = new AssetReference($this->am, 'foo');
    }

    protected function tearDown(): void
    {
        $this->am = null;
        $this->ref = null;
    }

    /**
     * @dataProvider getMethodAndRetVal
     */
    public function testMethods($method, $returnValue)
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();

        $this->am->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method($method)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->ref->$method(), '->'.$method.'() returns the asset value');
    }

    public function getMethodAndRetVal()
    {
        return array(
            array('getContent', 'asdf'),
            array('getSourceRoot', 'asdf'),
            array('getSourcePath', 'asdf'),
            array('getTargetPath', 'asdf'),
            array('getLastModified', 123),
        );
    }

    public function testLazyFilters()
    {
        $this->am->expects($this->never())->method('get');
        $this->ref->ensureFilter($this->getMockBuilder(FilterInterface::class)->getMock());
    }

    public function testFilterFlush()
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())->method('ensureFilter');
        $asset->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue([]));

        $this->ref->ensureFilter($this->getMockBuilder(FilterInterface::class)->getMock());

        $this->assertIsArray($this->ref->getFilters(), '->getFilters() flushes and returns filters');
    }

    public function testSetContent()
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();

        $this->am->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('setContent')
            ->with('asdf');

        $this->ref->setContent('asdf');
    }

    public function testLoad()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('load')
            ->with($filter);

        $this->ref->load($filter);
    }

    public function testDump()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('dump')
            ->with($filter);

        $this->ref->dump($filter);
    }

    public function testClone()
    {
        $filter1 = $this->getMockBuilder(FilterInterface::class)->getMock();
        $filter2 = $this->getMockBuilder(FilterInterface::class)->getMock();
        $filter3 = $this->getMockBuilder(FilterInterface::class)->getMock();

        $asset = new StringAsset('');
        $this->am->expects($this->any())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));

        $this->ref->ensureFilter($filter1);
        $this->ref->load();

        $clone1 = clone $this->ref;
        $clone1->ensureFilter($filter2);
        $clone1->load();

        $clone2 = clone $clone1;
        $clone2->ensureFilter($filter3);
        $clone2->load();

        $this->assertCount(1, $asset->getFilters());
        $this->assertCount(1, $this->ref->getFilters());
        $this->assertCount(2, $clone1->getFilters());
        $this->assertCount(3, $clone2->getFilters());
    }
}
