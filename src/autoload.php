<?php

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Assetic\\Test\\')) {
        $file = __DIR__ . 'Assetic/tests/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    } elseif (0 === strpos($class, 'Assetic\\')) {
        $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
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

require_once __DIR__ . '/../lessphp/lessc.inc.php';
