<?php namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\StylusFilter;

/**
 * @group integration
 */
class StylusFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp(): void
    {
        if (!$nodeBin = $this->findExecutable('node', 'NODE_BIN')) {
            $this->markTestSkipped('Unable to find `node` executable.');
        }

        if (!$this->checkNodeModule('stylus', $nodeBin)) {
            $this->markTestSkipped('The "stylus" module is not installed.');
        }

        if (!$stylusBin = $this->findExecutable('stylus', 'STYLUS_BIN')) {
            $this->markTestSkipped('The "stylus" bin could not be found.');
        }

        $this->filter = new StylusFilter($stylusBin);
    }

    protected function tearDown(): void
    {
        $this->filter = null;
    }

    public function testFilterLoad()
    {
        $asset = new StringAsset("body\n  font 12px Helvetica, Arial, sans-serif\n  color black");
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals("body {\n  font: 12px Helvetica, Arial, sans-serif;\n  color: #000;\n}\n", $asset->getContent(), '->filterLoad() parses the content');
    }

    public function testFilterLoadWithCompression()
    {
        $asset = new StringAsset("body\n  font 12px Helvetica, Arial, sans-serif\n  color black;");
        $asset->load();

        $this->filter->setCompress(true);
        $this->filter->filterLoad($asset);

        $this->assertEquals("body{font:12px Helvetica,Arial,sans-serif;color:#000}", $asset->getContent(), '->filterLoad() parses the content and compress it');

    }
}
