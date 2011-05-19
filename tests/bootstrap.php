<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Assetic\\Test\\')) {
        $file = __DIR__ . '/../tests/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    } elseif (0 === strpos($class, 'Assetic\\')) {
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    } elseif (isset($_SERVER['TWIG_LIB']) && 0 === strpos($class, 'Twig_')) {
        $file = $_SERVER['TWIG_LIB'] . '/' . str_replace('_', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
});

if (isset($_SERVER['LESSPHP'])) {
    require_once $_SERVER['LESSPHP'];
}

if (isset($_SERVER['CSSMIN'])) {
    require_once $_SERVER['CSSMIN'];
}

if (isset($_SERVER['PACKAGER'])) {
    require_once $_SERVER['PACKAGER'];
}
