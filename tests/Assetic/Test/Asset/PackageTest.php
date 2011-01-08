<?php

namespace Assetic\Test\Asset;

use Assetic\Asset\Package;

class PackageTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadFilter()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');

        $filter->expects($this->once())
            ->method('filterLoad')
            ->with($asset);

        $package = new Package(array($asset), array($filter));
        $package->load();
    }

    public function testDumpFilter()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');

        $filter->expects($this->once())
            ->method('filterDump')
            ->with($this->logicalAnd(
                $this->logicalNot($this->identicalTo($asset)),
                $this->isInstanceOf('Assetic\\Asset\\AssetInterface')
            ));

        $package = new Package(array($asset), array($filter));
        $package->dump();
    }

    public function testNestedPackageLoad()
    {
        $innerPackage = $this->getMock('Assetic\\Asset\\Package');
        $innerPackage->expects($this->once())->method('load');

        $outerPackage = new Package(array($innerPackage));
        $outerPackage->load();
    }

    /**
     * @dataProvider provideForLoadAssets
     */
    public function testLoadAssets($contents, $glue, $expected)
    {
        $assets = array();
        foreach ($contents as $content) {
            $assets[] = $asset = $this->getMock('Assetic\\Asset\\AssetInterface');
            $asset->expects($this->once())->method('load');
            $asset->expects($this->once())->method('getContent')->will($this->returnValue($content));
        }

        $package = new Package($assets);
        $package->load($glue);

        $this->assertEquals($expected, $package->getContent(), '->load() merges the content of the assets');
    }

    public function provideForLoadAssets()
    {
        return array(
            array(array('asdf1', 'asdf2'), ' ', 'asdf1 asdf2'),
            array(array('foo1', 'foo2'), "\n", "foo1\nfoo2"),
        );
    }
}
