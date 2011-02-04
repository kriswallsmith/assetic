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
 * Compiles CoffeeScript into Javascript.
 *
 * @link http://jashkenas.github.com/coffee-script/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CoffeeScriptFilter implements FilterInterface
{
    private $coffeePath;

    public function __construct($coffeePath = '/usr/bin/coffee')
    {
        $this->coffeePath = $coffeePath;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $input = tempnam(sys_get_temp_dir(), 'assetic_coffee');
        file_put_contents($input, $asset->getContent());

        $output = shell_exec(sprintf('cat %s | coffee -sc', escapeshellarg($input)));
        unlink($input);

        $asset->setContent($output);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
