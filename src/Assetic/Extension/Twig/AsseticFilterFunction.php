<?php namespace Assetic\Extension\Twig;

use Twig\TwigFunction;

class AsseticFilterFunction
{
    public static function make(AsseticExtension $extension, $name, $options = [])
    {
        return new TwigFunction($name, function ($input, array $options) use ($extension, $name) {
            return $extension->getFilterInvoker($name)->invoke($input, $options);
        }, $options);
    }
}
