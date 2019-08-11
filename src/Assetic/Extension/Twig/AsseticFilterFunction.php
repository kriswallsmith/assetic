<?php namespace Assetic\Extension\Twig;

class AsseticFilterFunction extends \Twig_SimpleFunction
{
    public function __construct($name, $options = array())
    {
        parent::__construct($name, null, array_merge($options, array(
            'needs_environment' => false,
            'needs_context' => false,
            'node_class' => '\Assetic\Extension\Twig\AsseticFilterNode',
        )));
    }
}
