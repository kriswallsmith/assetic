<?php namespace Assetic\Test\Factory;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Asset\AssetCollectionInterface;
use Assetic\Contracts\Factory\Worker\WorkerInterface;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Contracts\Filter\DependencyExtractorInterface;
use Assetic\AssetManager;
use Assetic\FilterManager;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\FileAsset;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetReference;
use Assetic\Factory\AssetFactory;

class AssetFactoryTest extends TestCase
{
    private $am;
    private $fm;
    private $factory;

    protected function setUp(): void
    {
        $this->am = $this->getMockBuilder(AssetManager::class)->getMock();
        $this->fm = $this->getMockBuilder(FilterManager::class)->getMock();

        $this->factory = new AssetFactory(__DIR__);
        $this->factory->setAssetManager($this->am);
        $this->factory->setFilterManager($this->fm);
    }

    protected function tearDown(): void
    {
        $this->am = null;
        $this->fm = null;
        $this->factory = null;
    }

    public function testNoAssetManagerReference()
    {
        $this->expectException(\LogicException::class, 'There is no asset manager.');

        $factory = new AssetFactory('.');
        $factory->createAsset(array('@foo'));
    }

    public function testNoAssetManagerNotReference()
    {
        $factory = new AssetFactory('.');
        $this->assertInstanceOf(AssetInterface::class, $factory->createAsset(array('foo')));
    }

    public function testNoFilterManager()
    {
        $this->expectException(\LogicException::class, 'There is no filter manager.');

        $factory = new AssetFactory('.');
        $factory->createAsset(array('foo'), array('foo'));
    }

    public function testCreateAssetReference()
    {
        $referenced = $this->getMockBuilder(AssetInterface::class)->getMock();

        $this->am->expects($this->any())
            ->method('get')
            ->with('jquery')
            ->will($this->returnValue($referenced));

        $assets = $this->factory->createAsset(array('@jquery'));
        $arr = iterator_to_array($assets);
        $this->assertInstanceOf(AssetReference::class, $arr[0], '->createAsset() creates a reference');
    }

    /**
     * @dataProvider getHttpUrls
     */
    public function testCreateHttpAsset($sourceUrl)
    {
        $assets = $this->factory->createAsset(array($sourceUrl));
        $arr = iterator_to_array($assets);
        $this->assertInstanceOf(HttpAsset::class, $arr[0], '->createAsset() creates an HTTP asset');
    }

    public function getHttpUrls()
    {
        return array(
            array('http://example.com/foo.css'),
            array('https://example.com/foo.css'),
            array('//example.com/foo.css'),
        );
    }

    public function testCreateFileAsset()
    {
        $assets = $this->factory->createAsset(array(basename(__FILE__)));
        $arr = iterator_to_array($assets);
        $this->assertInstanceOf(FileAsset::class, $arr[0], '->createAsset() creates a file asset');
    }

    public function testCreateGlobAsset()
    {
        $assets = $this->factory->createAsset(array('*'));
        $arr = iterator_to_array($assets);
        $this->assertInstanceOf(FileAsset::class, $arr[0], '->createAsset() uses a glob to create a file assets');
    }

    public function testCreateGlobAssetAndLoadFiles()
    {
        $assets = $this->factory->createAsset(array('*/Fixtures/*/*'));
        $assets->load();

        $this->assertEquals(5, count(iterator_to_array($assets)), '->createAsset() adds files');
    }

    public function testCreateGlobAssetAndExcludeDirectories()
    {
        $assets = $this->factory->createAsset(array('*/Fixtures/*', '*/Fixtures/*/*'));
        $assets->load();

        $this->assertEquals(5, count(iterator_to_array($assets)), '->createAsset() excludes directories and add files');
    }

    public function testCreateAssetCollection()
    {
        $asset = $this->factory->createAsset(array('*', basename(__FILE__)));
        $this->assertInstanceOf('Assetic\\Asset\\AssetCollection', $asset, '->createAsset() creates an asset collection');
    }

    public function testFilter()
    {
        $this->fm->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->getMockBuilder(FilterInterface::class)->getMock()));

        $asset = $this->factory->createAsset([], array('foo'));
        $this->assertEquals(1, count($asset->getFilters()), '->createAsset() adds filters');
    }

    public function testInvalidFilter()
    {
        $this->expectException('InvalidArgumentException');

        $this->fm->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->throwException(new \InvalidArgumentException()));

        $asset = $this->factory->createAsset([], array('foo'));
    }

    public function testOptionalInvalidFilter()
    {
        $this->factory->setDebug(true);

        $asset = $this->factory->createAsset([], array('?foo'));

        $this->assertEquals(0, count($asset->getFilters()), '->createAsset() does not add an optional invalid filter');
    }

    public function testIncludingOptionalFilter()
    {
        $this->fm->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->getMockBuilder(FilterInterface::class)->getMock()));

        $this->factory->createAsset(array('foo.css'), array('?foo'));
    }

    public function testWorkers()
    {
        $worker = $this->getMockBuilder(WorkerInterface::class)->getMock();

        // called once on the collection and once on each leaf
        $worker->expects($this->exactly(3))
            ->method('process')
            ->with($this->isInstanceOf(AssetInterface::class));

        $this->factory->addWorker($worker);
        $this->factory->createAsset(array('foo.js', 'bar.js'));
    }

    public function testWorkerReturn()
    {
        $worker = $this->getMockBuilder(WorkerInterface::class)->getMock();
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();

        $worker->expects($this->at(2))
            ->method('process')
            ->with($this->isInstanceOf(AssetCollectionInterface::class))
            ->will($this->returnValue($asset));

        $this->factory->addWorker($worker);
        $coll = $this->factory->createAsset(array('foo.js', 'bar.js'));

        $this->assertEquals(1, count(iterator_to_array($coll)));
    }

    public function testNestedFormula()
    {
        $this->fm->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($this->getMockBuilder(FilterInterface::class)->getMock()));

        $inputs = array(
            'css/main.css',
            array(
                // nested formula
                array('css/more.sass'),
                array('foo'),
            ),
        );

        $asset = $this->factory->createAsset($inputs, [], array('output' => 'css/*.css'));

        $i = 0;
        foreach ($asset as $leaf) {
            $i++;
        }

        $this->assertEquals(2, $i);
    }

    public function testGetLastModified()
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();
        $child = $this->getMockBuilder(AssetInterface::class)->getMock();
        $filter1 = $this->getMockBuilder(FilterInterface::class)->getMock();
        $filter2 = $this->getMockBuilder(DependencyExtractorInterface::class)->getMock();

        $asset->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(123));
        $asset->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue(array($filter1, $filter2)));
        $asset->expects($this->once())
            ->method('ensureFilter')
            ->with($filter1);
        $filter2->expects($this->once())
            ->method('getChildren')
            ->with($this->factory)
            ->will($this->returnValue(array($child)));
        $child->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(456));
        $child->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue([]));

        $this->assertEquals(456, $this->factory->getLastModified($asset));
    }

    public function testGetLastModifiedCollection()
    {
        $leaf = $this->getMockBuilder(AssetInterface::class)->getMock();
        $child = $this->getMockBuilder(AssetInterface::class)->getMock();
        $filter1 = $this->getMockBuilder(FilterInterface::class)->getMock();
        $filter2 = $this->getMockBuilder(DependencyExtractorInterface::class)->getMock();

        $asset = new AssetCollection();
        $asset->add($leaf);

        $leaf->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(123));
        $leaf->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue(array($filter1, $filter2)));
        $leaf->expects($this->once())
            ->method('ensureFilter')
            ->with($filter1);
        $filter2->expects($this->once())
            ->method('getChildren')
            ->with($this->factory)
            ->will($this->returnValue(array($child)));
        $child->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(456));
        $child->expects($this->any())
            ->method('getFilters')
            ->will($this->returnValue([]));

        $this->assertEquals(456, $this->factory->getLastModified($asset));
    }
}
