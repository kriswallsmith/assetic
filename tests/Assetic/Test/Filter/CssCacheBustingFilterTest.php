<?php
/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\CssCacheBustingFilter;

/**
 * Class CssCacheBustingFilterTest
 * @package Assetic\Test\Filter
 * @author Maximilian Reichel <info@phramz.com>
 */
class CssCacheBustingFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testUrls($version, $format, $expectedFormat, $inputUrl, $expectedUrl)
    {
        $asset = new StringAsset(sprintf($expectedFormat, $inputUrl));
        $asset->load();

        $filter = new CssCacheBustingFilter();
        $filter->setVersion($version);
        $filter->setFormat($format);
        $filter->filterDump($asset);

        $this->assertEquals(sprintf($expectedFormat, $expectedUrl), $asset->getContent());
    }

    public function provideUrls()
    {
        return array(
            // url variants
            array('v123', '%s?%s', 'body { background: url(%s); }', 'css/body.css', 'css/body.css?v123'),
            array('123', '%s?version=%s', 'body { background: url("%s"); }', 'css/body.css', 'css/body.css?version=123'),
            array('bar', '%s?foo=%s', 'body { background: url(\'%s\'); }', 'css/body.css', 'css/body.css?foo=bar'),

            // @import variants
            array('v123', '%s?%s', '@import "%s";', 'css/imports.css', 'css/imports.css?v123'),
            array('123', '%s?version=%s', '@import url(%s);', 'css/imports.css', 'css/imports.css?version=123'),
            array('bar', '%s?foo=%s', '@import url("%s");', 'css/imports.css', 'css/imports.css?foo=bar'),
            array('v123', '%s?%s', '@import url(\'%s\');', 'css/imports.css', 'css/imports.css?v123'),

            // IE AlphaImageLoader filter
            array('v123', '%s?%s', '.fix { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'%s\'); }', 'css/ie.css', 'css/ie.css?v123'),

            // data url
            array('v1', '%s?%s', '.grayscale { filter: url("%s"); }',
                'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\'/></filter></svg>#grayscale',
                'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\'/></filter></svg>#grayscale'
            ),
        );
    }

    /**
     * @dataProvider provideMultipleUrls
     */
    public function testMultipleUrls($version, $format, $expectedFormat, $inputUrl1, $expectedUrl1, $inputUrl2, $expectedUrl2)
    {
        $asset = new StringAsset(sprintf($expectedFormat, $inputUrl1, $inputUrl2));
        $asset->load();

        $filter = new CssCacheBustingFilter();
        $filter->setVersion($version);
        $filter->setFormat($format);
        $filter->filterDump($asset);

        $this->assertEquals(sprintf($expectedFormat, $expectedUrl1, $expectedUrl2), $asset->getContent());
    }

    public function provideMultipleUrls()
    {
        return array(
            // url variants
            array('v123', '%s?%s', 'body { background: url(%s); background: url(%s); }', 'css/body.css', 'css/body.css?v123', 'css/body.css', 'css/body.css?v123'),
            array('v123', '%s?%s', "body { background: url(%s); \nbackground: url('%s'); }", 'css/body.css', 'css/body.css?v123', 'css/body.css', 'css/body.css?v123'),
            array('v123', '%s?%s', 'body { background: url(%s); background: url(%s); }', 'css/body.css', 'css/body.css?v123', 'css/foo.css', 'css/foo.css?v123'),
            array('v123', '%s?%s', "body { background: url(%s); \nbackground: url('%s'); }", 'css/body.css', 'css/body.css?v123', 'css/foo.css', 'css/foo.css?v123'),

            // @import variants
            array('v123', '%s?%s', '@import "%s"; @import "%s";', 'css/imports.css', 'css/imports.css?v123', 'css/imports.css', 'css/imports.css?v123'),
            array('v123', '%s?%s', "@import \"%s\"; \n@import \"%s\";", 'css/imports.css', 'css/imports.css?v123', 'css/imports.css', 'css/imports.css?v123'),
            array('v123', '%s?%s', '@import "%s"; @import "%s";', 'css/imports.css', 'css/imports.css?v123', 'css/foo.css', 'css/foo.css?v123'),
            array('v123', '%s?%s', "@import \"%s\"; \n@import \"%s\";", 'css/imports.css', 'css/imports.css?v123', 'css/foo.css', 'css/foo.css?v123'),
            array('bar', '%s?foo=%s', '@import url("%s"); @import url("%s");', 'css/imports.css', 'css/imports.css?foo=bar', 'css/imports.css', 'css/imports.css?foo=bar'),
            array('bar', '%s?foo=%s', "@import url(\"%s\"); \n@import url(\"%s\");", 'css/imports.css', 'css/imports.css?foo=bar', 'css/imports.css', 'css/imports.css?foo=bar'),
            array('bar', '%s?foo=%s', '@import url("%s"); @import url("%s");', 'css/imports.css', 'css/imports.css?foo=bar', 'css/foo.css', 'css/foo.css?foo=bar'),
            array('bar', '%s?foo=%s', "@import url(\"%s\"); \n@import url(\"%s\");", 'css/imports.css', 'css/imports.css?foo=bar', 'css/foo.css', 'css/foo.css?foo=bar'),

            // IE AlphaImageLoader filter
            array('v123', '%s?%s', '.fix { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'%s\'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'%s\'); }', 'css/ie.css', 'css/ie.css?v123', 'css/ie.css', 'css/ie.css?v123'),
            array('v123', '%s?%s', ".fix { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='%s'); \nfilter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='%s'); }", 'css/ie.css', 'css/ie.css?v123', 'css/ie.css', 'css/ie.css?v123'),
            array('v123', '%s?%s', '.fix { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'%s\'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'%s\'); }', 'css/ie.css', 'css/ie.css?v123', 'css/foo.css', 'css/foo.css?v123'),
            array('v123', '%s?%s', ".fix { filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='%s'); \nfilter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='%s'); }", 'css/ie.css', 'css/ie.css?v123', 'css/foo.css', 'css/foo.css?v123'),
        );
    }
}
