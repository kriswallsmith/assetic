<?php

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\ReactJsxFilter;

class ReactJsxFilterTest extends FilterTestCase
{
    /**
     * @var ReactJsxFilter
     */
    private $filter;

    protected function setUp()
    {
        $jsxBin = $this->findExecutable('jsx', 'JSX_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$jsxBin) {
            $this->markTestSkipped("Unable to find `jsx` executable.");
        }

        $this->filter = new ReactJsxFilter($jsxBin, $nodeBin);
    }

    public function testFilterLoad()
    {
        $prolog = "/** @jsx React.DOM */\n";
        $expected = $prolog . 'React.renderComponent(React.createElement(HelloMessage, {name: "John"}), mountNode);';
        $asset = new StringAsset($prolog . 'React.renderComponent(<HelloMessage name="John" />, mountNode);');
        $asset->load();
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $this->clean($asset->getContent()));
    }

    private function clean($js)
    {
        return preg_replace('~^//.*\n\s*~m', '', $js);
    }
}
