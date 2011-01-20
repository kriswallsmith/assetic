<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Assetic\\')) {
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    } elseif (isset($_SERVER['BUZZ_DIR']) && 0 === strpos($class, 'Buzz\\')) {
        $file = $_SERVER['BUZZ_DIR'] . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
});

// this will register the CodeSniffer autoloader if it's there
@include_once 'PHP/CodeSniffer.php';
