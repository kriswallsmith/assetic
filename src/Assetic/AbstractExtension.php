<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic;

abstract class AbstractExtension implements ExtensionInterface
{
    public function initialize(EnvironmentInterface $env)
    {
    }

    public function getLoaderVisitors()
    {
        return array();
    }

    public function getProcessorVisitors()
    {
        return array();
    }
}
