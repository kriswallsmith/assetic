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

use Assetic\Util\Analyzer\AnalyzerState;

class AnalyzerStateTest extends \PHPUnit_Framework_TestCase
{
    protected $general = '/^(?<processed>.*?\R*)(?<unprocessed>(?<token>\'|"|\/\*)(.*))?$/su';
    protected $quoted = array(
        array(
            'begin'  => "'",
            'end'    => "'",
            'regexp' => '/^(?<processed>\'(.{0}|.*?[^\\\\])(?<token>\'))(?<unprocessed>.*)$/su',
        ),
        array(
            'begin'  => '"',
            'end'    => '"',
            'regexp' => '/^(?<processed>"(.{0}|.*?[^\\\\])(?<token>"))(?<unprocessed>.*)$/su',
        ),
    );
    protected $comment = array(
        array(
            'begin'  => '/*',
            'end'    => '*/',
            'regexp' => '/^(?<processed>\/\*.*?(?<token>\*\/))(?<unprocessed>.*)$/su',
        ),
    );
    protected $state;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->state = new AnalyzerState('general', $this->general);

        foreach ($this->quoted as $quotedItem) {
            $state = new AnalyzerState('quoted', $quotedItem['regexp'], array($quotedItem['end'] => $this->state));
            $this->state->addState($quotedItem['begin'], $state);
        }

        foreach ($this->comment as $commentItem) {
            $state = new AnalyzerState('comment', $commentItem['regexp'], array($commentItem['end'] => $this->state));
            $this->state->addState($commentItem['begin'], $state);
        }
    }

    public function testProcessSingleState()
    {
        $state = $this->state;
        $string = ".foo {display: block;}";

        $result = $state->process($string);

        $this->assertEquals($string, $result);
        $this->assertNull($state->getUnprocessed());
        $this->assertNull($state->getNextState());
    }

    public function testProcessSeveralStates()
    {
        $state = $this->state;
        $unprocessed = ".foo /* comment */ {display: block;}";
        $types = array();
        $values = array();
        $expectedTypes = array('general', 'comment', 'general');
        $expectedValues = array('.foo ', '/* comment */', ' {display: block;}');

        do {
            $types[] = $state->getType();
            $values[] = $state->process($unprocessed);
            $unprocessed = $state->getUnprocessed();
        } while ($state = $state->getNextState());

        $this->assertEquals($expectedTypes, $types);
        $this->assertEquals($expectedValues, $values);
    }

    public function testProcessStrict()
    {
        $this->setExpectedExceptionRegExp(
            "Exception",
            "/Cannot match pattern \[.*?\] against string \[.*?\]/su"
        );

        $state = $this->state;
        $string = ".foo '{display: block;}";

        $state->process($string, true);
        $unprocessed = $state->getUnprocessed();
        $state->getNextState()->process($unprocessed, true);
    }
}
