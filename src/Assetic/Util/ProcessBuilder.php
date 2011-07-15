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
    private $inheritEnv = false;

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

    public function inheritEnvironmentVariables($inheritEnv = true)
    {
        $this->inheritEnv = $inheritEnv;

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

    public function getProcess()
    {
        if (!count($this->parts)) {
            throw new \LogicException('You must add() command parts before calling getProcess().');
        }

        $parts = $this->parts;
        $cmd = array_shift($parts);
        $script = escapeshellcmd($cmd).' '.implode(' ', array_map('escapeshellarg', $parts));

        $env = $this->inheritEnv ? ($this->env ?: array()) + $_ENV : $this->env;

        return new Process($script, $this->cwd, $env, $this->stdin, $this->timeout, $this->options);
    }
}
