<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Util\Analyzer;

/**
 * CSS analyzer
 *
 * @author Alex Ash <streamprop@gmail.com>
 */
class CssAnalyzer
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
    protected $css;

    /**
     * Create analyzer for CSS
     *
     * @param string $css
     */
    public function __construct($css)
    {
        $this->css = $css;
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

    /**
     * Shows whether analyzer has more steps to pass
     *
     * @return bool
     */
    public function hasSteps()
    {
        return !is_null($this->css);
    }

    /**
     * Make one analyzer step
     *
     * Returns type and contents of analyzed part of CSS
     *
     * @return array - ['type' => 'typename', 'part' => 'analyzed_part']
     */
    public function step()
    {
        $type = $this->state->getType();
        $part = $this->state->process($this->css);

        $this->css = $this->state->getUnprocessed();
        $this->state = $this->state->getNextState();

        return array('type' => $type, 'part' => $part);
    }
}
