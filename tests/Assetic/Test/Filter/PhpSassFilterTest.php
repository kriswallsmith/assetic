<?php
/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\PhpSassFilter;

/**
 * @group integration
 */
class PhpSassFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @internal
     */
    private function getFilter($compass = false)
    {
        $filter = new PhpSassFilter();

        if ($compass) {
            $filter->setCompass(true);
        }

        return $filter;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists('SassParser')) {
            $this->markTestSkipped('phpsass is not installed');
        }
    }

    public function testFilterLoad()
    {
        $expected = <<<EOF
img {
  width: 100; }


EOF;

        $asset = new StringAsset(<<<EOF
// test basic functionality
img{
 width:100}
EOF
        );
        $asset->load();

        $this->getFilter()->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterLoad() parses the content');
    }

    public function testAddLoadPath()
    {
        $expected = <<<EOF
#test { color: red; }


EOF;

        $asset = new StringAsset(<<<EOF
// test load path and tyle
@import '_import.scss';

#test {
 color:\$red}
EOF
        );
        $asset->load();

        $this->getFilter()
             ->addLoadPath(__DIR__.'/fixtures/sass/import_path')
             ->setStyle(\SassRenderer::STYLE_COMPACT)
             ->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent(), 'Import paths are correctly used');
    }
}
