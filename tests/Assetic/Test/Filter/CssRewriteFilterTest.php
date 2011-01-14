<?php

namespace Assetic\Test\Filter;

use Assetic\Asset\Asset;
use Assetic\Filter\CssRewriteFilter;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CssRewriteFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('PHP_CodeSniffer_Tokenizers_CSS')) {
            $this->markTestSkipped('CodeSniffer is not installed.');
        }
    }

    public function testInterface()
    {
        $filter = new CssRewriteFilter(new \PHP_CodeSniffer_Tokenizers_CSS());
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'CssRewriteFilter implements FilterInterface');
    }

    /**
     * @group functional
     * @dataProvider provideUrls
     */
    public function testUrls($format, $source, $target, $inputUrl, $expectedUrl)
    {
        $context = $this->getMock('Assetic\\Asset\\AssetInterface');
        $context->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($target));

        $asset = new Asset(sprintf($format, $inputUrl));
        $asset->setUrl($source);
        $asset->setContext($context);
        $asset->load();

        $filter = new CssRewriteFilter(new \PHP_CodeSniffer_Tokenizers_CSS());
        $filter->filterDump($asset);

        $this->assertEquals(sprintf($format, $expectedUrl), $asset->getBody(), '->filterDump() rewrites relative urls');
    }

    public function provideUrls()
    {
        return array(
            // url variants
            array('body { background: url(%s); }', 'css/body.css', 'css/build/main.css', '../images/bg.gif', '../../images/bg.gif'),
            array('body { background: url("%s"); }', 'css/body.css', 'css/build/main.css', '../images/bg.gif', '../../images/bg.gif'),
            array('body { background: url(\'%s\'); }', 'css/body.css', 'css/build/main.css', '../images/bg.gif', '../../images/bg.gif'),

            // @import variants
            array('@import "%s";', 'css/imports.css', 'css/build/main.css', 'import.css', '../import.css'),
            array('@import url(%s);', 'css/imports.css', 'css/build/main.css', 'import.css', '../import.css'),
            array('@import url("%s");', 'css/imports.css', 'css/build/main.css', 'import.css', '../import.css'),
            array('@import url(\'%s\');', 'css/imports.css', 'css/build/main.css', 'import.css', '../import.css'),

            // path diffs
            array('body { background: url(%s); }', 'css/body/bg.css', 'css/build/main.css', '../../images/bg.gif', '../../images/bg.gif'),
            array('body { background: url(%s); }', 'http://foo.com/css/body/bg.css', 'http://bar.com/css/build/main.css', '../../images/bg.gif', 'http://foo.com/images/bg.gif'),

            // url diffs
            array('body { background: url(%s); }', 'css/body.css', 'css/build/main.css', 'http://foo.com/bar.gif', 'http://foo.com/bar.gif'),
            array('body { background: url(%s); }', 'css/body.css', 'css/build/main.css', '/images/foo.gif', '/images/foo.gif'),
        );
    }
}
