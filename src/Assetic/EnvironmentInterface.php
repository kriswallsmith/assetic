<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic;

use Assetic\Tree\TraverserInterface;

/**
 * The environment provides access to core services.
 */
interface EnvironmentInterface
{
    function getExtensions();
    function addExtension(ExtensionInterface $extension);

    function initialize();
    function getLoader();
    function getProcessor();

    function load($logicalPath);
}
