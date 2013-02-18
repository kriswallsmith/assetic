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

use Assetic\Asset\StringAsset;
use Assetic\Filter\LessphpFilter;

/**
 * @group integration
 * @property LessphpFilter $filter
 */
class LessphpFilterTest extends LessFilterTest
{
    protected function setUp()
    {
        if (!class_exists('lessc')) {
            $this->markTestSkipped('LessPHP is not installed');
        }

        $this->filter = new LessphpFilter();
    }

    public function testPresets()
    {
        $asset = new StringAsset('.foo { color: @bar }');
        $asset->load();

        $this->filter->setPresets(array('bar' => 'green'));
        $this->filter->filterLoad($asset);

        $this->assertContains('green', $asset->getContent(), '->setPresets() to pass variables into lessphp filter');
    }

    public function testFormatterLessjs()
    {
        $asset = new StringAsset('.foo { color: green; }');
        $asset->load();

        $this->filter->setFormatter('lessjs');
        $this->filter->filterLoad($asset);

        $this->assertContains("\n  color", $asset->getContent(), '->setFormatter("lessjs")');
    }

    public function testFormatterCompressed()
    {
        $asset = new StringAsset('.foo { color: green; }');
        $asset->load();

        $this->filter->setFormatter('compressed');
        $this->filter->filterLoad($asset);

        $this->assertContains('color:green', $asset->getContent(), '->setFormatter("compressed")');
    }

    public function testFormatterClassic()
    {
        $asset = new StringAsset('.foo { color: green; }');
        $asset->load();

        $this->filter->setFormatter('classic');
        $this->filter->filterLoad($asset);

        $this->assertContains('{ color:green; }', $asset->getContent(), '->setFormatter("classic")');
    }

    public function testPreserveCommentsTrue()
    {
        $asset = new StringAsset("/* Line 1 */\n.foo { color: green }");
        $asset->load();

        $this->filter->setPreserveComments(true);
        $this->filter->filterLoad($asset);

        $this->assertContains('/* Line 1 */', $asset->getContent(), '->setPreserveComments(true)');
    }

    public function testPreserveCommentsFalse()
    {
        $asset = new StringAsset("/* Line 1 */\n.foo { color: green }");
        $asset->load();

        $this->filter->setPreserveComments(false);
        $this->filter->filterLoad($asset);

        $this->assertNotContains('/* Line 1 */', $asset->getContent(), '->setPreserveComments(false)');
    }
}
