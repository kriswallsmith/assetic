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

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

abstract class FilterTestCase extends \PHPUnit_Framework_TestCase
{
    protected function assertMimeType($expected, $data, $message = null)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $this->assertEquals($expected, $finfo->buffer($data), $message);
    }

    protected function findExecutable($name, $serverKey = null)
    {
        if ($serverKey && isset($_SERVER[$serverKey])) {
            return $_SERVER[$serverKey];
        }

        $finder = new ExecutableFinder();

        return $finder->find($name);
    }

    protected function checkNodeModule($module, $bin = null)
    {
        if (!$bin && !$bin = $this->findExecutable('node', 'NODE_BIN')) {
            $this->markTestSkipped('Unable to find `node` executable.');
        }

        $pb = new ProcessBuilder(array($bin, '-e', 'require(\''.$module.'\')'));

        if (isset($_SERVER['NODE_PATH'])) {
            $pb->setEnv('NODE_PATH', $_SERVER['NODE_PATH']);
        }

        return 0 === $pb->getProcess()->run();
    }
}
