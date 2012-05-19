<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core\Processor;

use Assetic\Asset\AssetInterface;
use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

class Context
{
    public function createTempFile($prefix = null, & $path = null)
    {
        $filesystem = new Filesystem(new Local(sys_get_temp_dir()));

        if ($prefix) {
            $file = $filesystem->createFile('assetic/'.$prefix.'/'.uniqid());
        } else {
            $file = $filesystem->createFile('assetic/'.uniqid());
        }

        $path = $filesystem->getAdapter()->computePath($file->getKey());

        return $file;
    }

    public function createProcessBuilder(array $arguments = array())
    {
        return new ProcessBuilder($arguments);
    }

    public function findExecutable($name, $default = null)
    {
        $finder = new ExecutableFinder();

        return $finder->find($name, $default);
    }

    public function isAssetProcessedBy(AssetInterface $asset, $key)
    {
        return in_array($key, $asset->getAttribute('processed', array()));
    }

    public function markAssetProcessedBy(AssetInterface $asset, $key)
    {
        $processed = $asset->getAttribute('processed', array());
        $processed[] = $key;
        $asset->setAttribute('processed', $processed);
    }
}
