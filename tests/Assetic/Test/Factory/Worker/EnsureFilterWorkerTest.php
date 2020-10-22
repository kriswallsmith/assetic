<?php namespace Assetic\Test\Factory\Worker;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\EnsureFilterWorker;

class EnsureFilterWorkerTest extends TestCase
{
    public function testMatch()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();
        $factory = $this->getMockBuilder(AssetFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $asset->expects($this->once())
            ->method('getTargetPath')
            ->will($this->returnValue('css/main.css'));
        $asset->expects($this->once())
            ->method('ensureFilter')
            ->with($filter);

        $worker = new EnsureFilterWorker('/\.css$/', $filter);
        $worker->process($asset, $factory);
    }

    public function testNonMatch()
    {
        $filter = $this->getMockBuilder(FilterInterface::class)->getMock();
        $asset = $this->getMockBuilder(AssetInterface::class)->getMock();
        $factory = $this->getMockBuilder(AssetFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $asset->expects($this->once())
            ->method('getTargetPath')
            ->will($this->returnValue('js/all.js'));
        $asset->expects($this->never())->method('ensureFilter');

        $worker = new EnsureFilterWorker('/\.css$/', $filter);
        $worker->process($asset, $factory);
    }
}
