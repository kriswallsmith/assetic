<?php

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\ReactJsxFilter;

class ReactJsxFilterTest extends FilterTestCase
{
    /**
     * @var string
     */
    private $jsxBin;

    /**
     * @var string
     */
    private $nodeBin;

    protected function setUp()
    {
        $this->jsxBin = $this->findExecutable('jsx', 'JSX_BIN');
        $this->nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$this->jsxBin) {
            $this->markTestSkipped("Unable to find `jsx` executable.");
        }
    }

    /**
     * @param \Assetic\Filter\ReactJsxFilter $filter
     */
    protected function filterLoad(ReactJsxFilter $filter)
    {
        $prolog = "/** @jsx React.DOM */\n";
        $expected = $prolog . 'React.renderComponent(React.createElement(HelloMessage, {name: "John"}), mountNode);';
        $asset = new StringAsset($prolog . 'React.renderComponent(<HelloMessage name="John" />, mountNode);');
        $asset->load();
        $filter->filterLoad($asset);

        $this->assertEquals($expected, $this->clean($asset->getContent()));
    }

    public function testGlobalJsxBin()
    {
        $filter = new ReactJsxFilter($this->jsxBin, $this->nodeBin);
        $this->filterLoad($filter);
    }

    public function testLocalJsxBin()
    {
        $filter = new ReactJsxFilter('/non/existent/jsx', $this->nodeBin, array(
            __DIR__ . '/../../../../node_modules',
        ));
        $this->filterLoad($filter);
    }

    private function clean($js)
    {
        return preg_replace('~^//.*\n\s*~m', '', $js);
    }
}
