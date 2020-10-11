<?php namespace Assetic\Test\Filter;

use Assetic\Test\TestCase;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

abstract class FilterTestCase extends TestCase
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

        // update the path (emulates logic in ExecutableFinder)
        $paths = array(__DIR__ . '/../../../../node_modules/.bin');
        if ($current = ini_get('open_basedir')) {
            ini_set('open_basedir', $this->ensurePaths($current, $paths));
        } else {
            $varname = getenv('PATH') ? 'PATH' : 'Path';
            putenv(sprintf('%s=%s', $varname, $this->ensurePaths(getenv($varname), $paths)));
        }

        $finder = new ExecutableFinder();

        return $finder->find($name);
    }

    protected function checkNodeModule($module, $bin = null)
    {
        if (!$bin && !$bin = $this->findExecutable('node', 'NODE_BIN')) {
            $this->markTestSkipped('Unable to find `node` executable.');
        }

        $pb = new Process(array($bin, '-e', 'require(\''.$module.'\')'));

        if (isset($_SERVER['NODE_PATH'])) {
            $pb->setEnv(['NODE_PATH' => $_SERVER['NODE_PATH']]);
        }

        return 0 === $pb->run();
    }

    private function ensurePaths($current, array $paths)
    {
        foreach ($paths as $path) {
            if (!preg_match(sprintf('~(^|%s)%s(%1$s|$)~', PATH_SEPARATOR, preg_quote($path, '~')), $current)) {
                $current .= PATH_SEPARATOR.$path;
            }
        }

        return $current;
    }
}
