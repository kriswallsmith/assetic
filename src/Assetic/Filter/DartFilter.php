<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Compiles Dart into Javascript.
 *
 * @link http://dartlang.org/
 */
class DartFilter implements FilterInterface
{
    private $dartPath;

    public function __construct($dartPath = '/usr/bin/dart2js')
    {
        $this->dartPath = $dartPath;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $input = tempnam(sys_get_temp_dir(), 'assetic_dart');
        file_put_contents($input, $asset->getContent());

        $output = tempnam(sys_get_temp_dir(), 'assetic_dart_output');

        $pb = new ProcessBuilder(array(
            $this->dartPath,
            '-o'.$output
        ));

        $pb->add($input);
        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent(file_get_contents($output));
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
