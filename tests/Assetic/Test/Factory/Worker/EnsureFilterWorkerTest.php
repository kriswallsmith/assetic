<?php namespace Assetic\Test\Factory\Worker;

use Assetic\Factory\Worker\EnsureFilterWorker;

class EnsureFilterWorkerTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $filter = $this->getMockBuilder('Assetic\\Contracts\\Filter\\FilterInterface')->getMock();
        $asset = $this->getMockBuilder('Assetic\\Contracts\\Asset\\AssetInterface')->getMock();
        $factory = $this->getMockBuilder('Assetic\\Factory\\AssetFactory')
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
        $filter = $this->getMockBuilder('Assetic\\Contracts\\Filter\\FilterInterface')->getMock();
        $asset = $this->getMockBuilder('Assetic\\Contracts\\Asset\\AssetInterface')->getMock();
        $factory = $this->getMockBuilder('Assetic\\Factory\\AssetFactory')
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
