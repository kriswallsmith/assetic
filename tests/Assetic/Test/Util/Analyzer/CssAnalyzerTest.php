<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Util\Analyzer;

use Assetic\Util\Analyzer\CssAnalyzer;

class CssAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function testHasStepsContentNotNull()
    {
        $analyzer = new CssAnalyzer("some content");

        $has = $analyzer->hasSteps();

        $this->assertTrue($has);
    }

    public function testHasStepsNullContent()
    {
        $analyzer = new CssAnalyzer(null);

        $has = $analyzer->hasSteps();

        $this->assertFalse($has);
    }

    public function testStepSingleState()
    {
        $content = ".foo {display: block;}";
        $analyzer = new CssAnalyzer($content);

        $result = $analyzer->step();

        $this->assertEquals('general', $result['type']);
        $this->assertEquals($content, $result['part']);
        $this->assertFalse($analyzer->hasSteps());
    }

    public function testStepSeveralStates()
    {
        $content = ".foo /* comment */ {display: block;}";
        $analyzer = new CssAnalyzer($content);
        $types = array();
        $parts = array();
        $expectedTypes = array('general', 'comment', 'general');
        $expectedParts = array('.foo ', '/* comment */', ' {display: block;}');

        while ($analyzer->hasSteps()) {
            $step = $analyzer->step();
            $types[] = $step['type'];
            $parts[] = $step['part'];
        }

        $this->assertEquals($expectedTypes, $types);
        $this->assertEquals($expectedParts, $parts);
    }
}
