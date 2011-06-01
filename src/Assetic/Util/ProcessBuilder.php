<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Util;

/**
 * Process builder.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class ProcessBuilder
{
    private $parts = array();
    private $cwd;
    private $env;
    private $stdin;
    private $timeout = 60;
    private $options = array();
    private $escaper;

    public function add($part)
    {
        $this->parts[] = $part;

        return $this;
    }

    public function setWorkingDirectory($cwd)
    {
        $this->cwd = $cwd;

        return $this;
    }

    public function setEnv($name, $value)
    {
        if (null === $this->env) {
            $this->env = array();
        }

        $this->env[$name] = $value;

        return $this;
    }

    public function setInput($stdin)
    {
        $this->stdin = $stdin;

        return $this;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function setEscaper(\Closure $escaper)
    {
        $this->escaper = $escaper;

        return $this;
    }

    public function getProcess()
    {
        $escaper = $this->escaper ?: function($value)
        {
            return false === strpos($value, ' ') ? $value : escapeshellarg($value);
        };

        return new Process(implode(' ', array_map($escaper, $this->parts)), $this->cwd, $this->env, $this->stdin, $this->timeout, $this->options);
    }
}
