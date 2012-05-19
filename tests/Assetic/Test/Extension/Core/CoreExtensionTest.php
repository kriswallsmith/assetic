<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Core;

use Assetic\Extension\Core\CoreExtension;
use Assetic\Extension\Core\Visitor\ProcessorVisitor;

class CoreExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    protected function setUp()
    {
        $this->extension = new CoreExtension();
    }

    protected function tearDown()
    {
        unset($this->extension);
    }

    /**
     * @test
     */
    public function shouldSortProcessorsByPriority()
    {
        $p1 = $this->getMock('Assetic\Extension\Core\Processor\ProcessorInterface');
        $p2 = $this->getMock('Assetic\Extension\Core\Processor\ProcessorInterface');
        $asset = $this->getMock('Assetic\Asset\AssetInterface');

        $asset->expects($this->at(0))
            ->method('getAttribute')
            ->with('foo');
        $asset->expects($this->at(1))
            ->method('getAttribute')
            ->with('bar');
        $p1->expects($this->once())
            ->method('process')
            ->with($asset)
            ->will($this->returnCallback(function($asset) {
                $asset->getAttribute('foo');
            }));
        $p2->expects($this->once())
            ->method('process')
            ->with($asset)
            ->will($this->returnCallback(function($asset) {
                $asset->getAttribute('bar');
            }));

        $this->extension->registerProcessor($p1, null, 10);
        $this->extension->registerProcessor($p2, null, 0);

        foreach ($this->extension->getLoaderVisitors() as $visitor) {
            if ($visitor instanceof ProcessorVisitor) {
                $visitor->enter($asset);
                break;
            }
        }
    }
}
