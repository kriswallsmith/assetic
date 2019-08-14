<?php namespace Assetic\Test\Filter;

use PHPUnit\Framework\TestCase;
use Assetic\Asset\FileAsset;
use Assetic\Filter\PackagerFilter;

/**
 * @group integration
 */
class PackagerFilterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('Packager')) {
            $this->markTestSkipped('Packager is not available.');
        }
    }

    public function testPackager()
    {
        $expected = <<<EOF
/*
---

name: Util

provides: [Util]

...
*/

function foo() {}


/*
---

name: App

requires: [Util/Util]

...
*/

var bar = foo();


EOF;

        $asset = new FileAsset(__DIR__.'/fixtures/packager/app/application.js', array(), __DIR__.'/fixtures/packager/app', 'application.js');
        $asset->load();

        $filter = new PackagerFilter();
        $filter->addPackage(__DIR__.'/fixtures/packager/lib');
        $filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() runs packager');
    }
}
