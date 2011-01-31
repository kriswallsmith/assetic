<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\CssRewriteFilter;

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
    public function testUrls($format, $sourceUrl, $targetUrl, $inputUrl, $expectedUrl)
    {
        $asset = new StringAsset(sprintf($format, $inputUrl), $sourceUrl);
        $asset->load();

        $filter = new CssRewriteFilter(new \PHP_CodeSniffer_Tokenizers_CSS());
        $filter->filterLoad($asset);
        $filter->filterDump($asset, $targetUrl);

        $this->assertEquals(sprintf($format, $expectedUrl), $asset->getContent(), '->filterDump() rewrites relative urls');
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

    public function testNoTargetUrl()
    {
        $content = 'body{url(foo.gif)}';

        $asset = new StringAsset($content);
        $asset->load();

        $filter = new CssRewriteFilter(new \PHP_CodeSniffer_Tokenizers_CSS());
        $filter->filterLoad($asset);
        $filter->filterDump($asset);

        $this->assertEquals($content, $asset->getContent(), '->filterDump() urls are not changed without urls');
    }
}
