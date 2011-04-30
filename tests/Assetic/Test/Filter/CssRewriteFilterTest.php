<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\CssRewriteFilter;

class CssRewriteFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testUrls($format, $sourceUrl, $targetUrl, $inputUrl, $expectedUrl)
    {
        $asset = new StringAsset(sprintf($format, $inputUrl), array(), $sourceUrl);
        $asset->setTargetUrl($targetUrl);
        $asset->load();

        $filter = new CssRewriteFilter();
        $filter->filterLoad($asset);
        $filter->filterDump($asset);

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
            array('body { background: url(%s); }', 'css/body.css', 'main.css', '../images/bg.gif', 'css/../images/bg.gif'), // fixme
            array('body { background: url(%s); }', 'body.css', 'css/main.css', 'images/bg.gif', '../images/bg.gif'),
            array('body { background: url(%s); }', 'source/css/body.css', 'output/build/main.css', '../images/bg.gif', '../../source/images/bg.gif'),
            array('body { background: url(%s); }', 'css/body.css', 'css/build/main.css', '//example.com/images/bg.gif', '//example.com/images/bg.gif'),

            // url diffs
            array('body { background: url(%s); }', 'css/body.css', 'css/build/main.css', 'http://foo.com/bar.gif', 'http://foo.com/bar.gif'),
            array('body { background: url(%s); }', 'css/body.css', 'css/build/main.css', '/images/foo.gif', '/images/foo.gif'),
            array('body { background: url(%s); }', 'css/body.css', 'css/build/main.css', 'http://foo.com/images/foo.gif', 'http://foo.com/images/foo.gif'),
        );
    }

    public function testNoTargetUrl()
    {
        $content = 'body{url(foo.gif)}';

        $asset = new StringAsset($content);
        $asset->load();

        $filter = new CssRewriteFilter();
        $filter->filterLoad($asset);
        $filter->filterDump($asset);

        $this->assertEquals($content, $asset->getContent(), '->filterDump() urls are not changed without urls');
    }
}
