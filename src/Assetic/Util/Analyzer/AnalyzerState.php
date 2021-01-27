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
 * State of string analyzer
 *
 * @author Alex Ash <streamprop@gmail.com>
 */
class AnalyzerState
{
    private $type;
    private $regexp;
    private $token;
    private $states;
    private $unprocessed;

    /**
     * Create AnalyzerState
     *
     * Regexp should contain three named groups:
     *  - 'processed' should contain pattern for the current state
     *  - 'token' should contain pattern for transition from current state to next
     *  - 'unprocessed' should contain pattern for unprocessed piece of string (may contain token)
     *
     * @param string          $type   - type of state (ex. 'comment', 'string', 'general', etc)
     * @param string          $regexp - regexp to match against analyzing string
     * @param AnalyzerState[] $states - array of token => state mappings, used to move between states
     */
    public function __construct($type, $regexp, array $states = array())
    {
        $this->type = $type;
        $this->regexp = $regexp;
        $this->states = $states;
        $this->clean();
    }

    private function clean()
    {
        $this->token = null;
        $this->unprocessed = null;
    }

    /**
     * Define state links
     *
     * @param array $states - array of token => state mappings, used to move between states
     *
     * @return void
     */
    public function setStates(array $states)
    {
        $this->states = $states;
    }

    /**
     * Add state link
     *
     * @param string $token
     * @param AnalyzerState $state
     *
     * @return void
     */
    public function addState($token, $state)
    {
        $this->states[$token] = $state;
    }

    /**
     * Get current state type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get next state after processing string part
     *
     * @return AnalyzerState|null
     */
    public function getNextState()
    {
        return array_key_exists($this->token, $this->states)
            ? $this->states[$this->token]
            : null;
    }

    /**
     * Get unprocessed part of the string
     *
     * @return string
     */
    public function getUnprocessed()
    {
        return $this->unprocessed;
    }

    /**
     * Process string using regexp setted
     *
     * If called in strict mode and regexp doesn't match against string, exception is throwed,
     * else the whole string is returned
     *
     * @param string $string - string to process
     * @param bool   $strict - mode of processing
     *
     * @return string
     *
     * @throws Exception
     */
    public function process($string, $strict = false)
    {
        $this->clean();
        if(!preg_match($this->regexp, $string, $matches)) {
            if ($strict) {
                throw new \Exception("Cannot match pattern [{$this->regexp}] against string [$string]");
            }

            return $string;
        }

        $part = $matches['processed'];
        $this->token = array_key_exists('token', $matches) ? $matches['token'] : null;
        $this->unprocessed = array_key_exists('unprocessed', $matches) ? $matches['unprocessed'] : null;

        return $part;
    }
}
