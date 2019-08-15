<?php namespace Assetic\Test\Factory;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Factory\Resource\ResourceInterface;
use Assetic\Contracts\Factory\Loader\FormulaLoaderInterface;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\AssetFactory;

class LazyAssetManagerTest extends TestCase
{
    private $factory;
    private $am;

    protected function setUp(): void
    {
        $this->factory = $this->getMockBuilder(AssetFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->am = new LazyAssetManager($this->factory);
    }

    protected function tearDown(): void
    {
        $this->factory = null;
        $this->am = null;
    }

    public function testGetFromLoader()
    {
        $resource = $this->getMockBuilder(ResourceInterface::class)->getMock();
        $loader = $this->getMockBuilder(FormulaLoaderInterface::class)->getMock();
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();

        $formula = array(
            array('js/core.js', 'js/more.js'),
            array('?yui_js'),
            array('output' => 'js/all.js'),
        );

        $loader->expects($this->once())
            ->method('load')
            ->with($resource)
            ->will($this->returnValue(array('foo' => $formula)));
        $this->factory->expects($this->once())
            ->method('createAsset')
            ->with($formula[0], $formula[1], $formula[2] + array('name' => 'foo'))
            ->will($this->returnValue($asset));

        $this->am->setLoader('foo', $loader);
        $this->am->addResource($resource, 'foo');

        $this->assertSame($asset, $this->am->get('foo'), '->get() returns an asset from the loader');

        // test the "once" expectations
        $this->am->get('foo');
    }

    public function testGetResources()
    {
        $resources = array(
            $this->getMockBuilder(ResourceInterface::class)->getMock(),
            $this->getMockBuilder(ResourceInterface::class)->getMock(),
        );

        $this->am->addResource($resources[0], 'foo');
        $this->am->addResource($resources[1], 'bar');

        $ret = $this->am->getResources();

        foreach ($resources as $resource) {
            $this->assertTrue(in_array($resource, $ret, true));
        }
    }

    public function testGetResourcesEmpty()
    {
        $this->assertIsArray($this->am->getResources());
    }

    public function testSetFormula()
    {
        $this->am->setFormula('foo', []);
        $this->am->load();
        $this->assertTrue($this->am->hasFormula('foo'), '->load() does not remove manually added formulae');
    }

    public function testIsDebug()
    {
        $this->factory->expects($this->once())
            ->method('isDebug')
            ->will($this->returnValue(false));

        $this->assertSame(false, $this->am->isDebug(), '->isDebug() proxies the factory');
    }

    public function testGetLastModified()
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();

        $this->factory->expects($this->once())
            ->method('getLastModified')
            ->will($this->returnValue(123));

        $this->assertSame(123, $this->am->getLastModified($asset));
    }
}
