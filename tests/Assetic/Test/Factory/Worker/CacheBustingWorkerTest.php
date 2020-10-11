<?php namespace Assetic\Test\Factory\Worker;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;

class CacheBustingWorkerTest extends TestCase
{
    private $worker;

    protected function setUp(): void
    {
        $this->worker = new CacheBustingWorker();
    }

    protected function tearDown(): void
    {
        $this->worker = null;
    }

    /**
     * @test
     */
    public function shouldApplyHash()
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();
        $factory = $this->getMockBuilder(AssetFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue('css/main.css'));
        $factory->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->once())
            ->method('setTargetPath')
            ->with($this->logicalAnd(
                $this->stringStartsWith('css/main-'),
                $this->stringEndsWith('.css')
            ));

        $this->worker->process($asset, $factory);
    }

    /**
     * @test
     */
    public function shouldApplyConsistentHash()
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();
        $factory = $this->getMockBuilder(AssetFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $paths = [];

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnValue('css/main.css'));
        $factory->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->exactly(2))
            ->method('setTargetPath')
            ->will($this->returnCallback(function ($path) use (&$paths) {
                $paths[] = $path;
            }));

        $this->worker->process($asset, $factory);
        $this->worker->process($asset, $factory);

        $this->assertCount(2, $paths);
        $this->assertCount(1, array_unique($paths));
    }

    /**
     * @test
     */
    public function shouldNotReapplyHash()
    {
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();
        $factory = $this->getMockBuilder(AssetFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $path = null;

        $asset->expects($this->any())
            ->method('getTargetPath')
            ->will($this->returnCallback(function () use (&$path) {
                return $path ?: 'css/main.css';
            }));
        $factory->expects($this->any())
            ->method('getLastModified')
            ->will($this->returnValue(1234));
        $asset->expects($this->once())
            ->method('setTargetPath')
            ->will($this->returnCallback(function ($arg) use (&$path) {
                $path = $arg;
            }));

        $this->worker->process($asset, $factory);
        $this->worker->process($asset, $factory);
    }
}
