<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Util;

use Assetic\Util\ProcessBuilder;

class ProcessBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldInheritEnvironmentVars()
    {
        $snapshot = $_ENV;
        $_ENV = $expected = array('foo' => 'bar');

        $pb = new ProcessBuilder();
        $pb->add('foo')->inheritEnvironmentVariables();
        $proc = $pb->getProcess();

        $this->assertEquals($expected, $proc->getEnv(), '->inheritEnvironmentVariables() copies $_ENV');

        $_ENV = $snapshot;
    }

    /**
     * @test
     */
    public function shouldNotReplaceExplicitlySetVars()
    {
        $snapshot = $_ENV;
        $_ENV = array('foo' => 'bar');
        $expected = array('foo' => 'baz');

        $pb = new ProcessBuilder();
        $pb
            ->setEnv('foo', 'baz')
            ->inheritEnvironmentVariables()
            ->add('foo')
        ;
        $proc = $pb->getProcess();

        $this->assertEquals($expected, $proc->getEnv(), '->inheritEnvironmentVariables() copies $_ENV');

        $_ENV = $snapshot;
    }
}
