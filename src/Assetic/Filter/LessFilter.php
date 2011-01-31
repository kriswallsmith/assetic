<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Loads LESS files.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class LessFilter implements FilterInterface
{
    private $lessPath;
    private $debug;

    public function __construct($lessPath = '/usr/bin/lessc')
    {
        $this->lessPath = $lessPath;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $options = array($this->lessPath);

        if ($this->debug) {
            $options[] = '--debug';
        }

        // finally
        $options[] = $input = tempnam(sys_get_temp_dir(), 'assetic_less');
        $options[] = $output = tempnam(sys_get_temp_dir(), 'assetic_less');

        // todo: check for a valid return code
        shell_exec(implode(' ', array_map('escapeshellarg', $options)));

        $asset->setContent(file_get_contents($output));

        // cleanup
        unlink($input);
        unlink($output);
    }

    public function filterDump(AssetInterface $asset, $targetUrl = null)
    {
    }
}
