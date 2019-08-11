<?php namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\RooleFilter;

/**
 * @group integration
 */
class RooleFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        $rooleBin = $this->findExecutable('roole', 'ROOLE_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$rooleBin) {
            $this->markTestSkipped('Unable to find `roole` executable.');
        }

        $this->filter = new RooleFilter($rooleBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->filter = null;
    }

    public function testFilterLoad()
    {
        $source = <<<'ROOLE'
$margin = 30px;

body {
  margin: $margin;
}

ROOLE;

        $asset = new StringAsset($source);
        $asset->load();

        $this->filter->filterLoad($asset);

        $content = $asset->getContent();
        $this->assertNotContains('$margin', $content);
        $this->assertContains('margin: 30px;', $content);
    }
}
