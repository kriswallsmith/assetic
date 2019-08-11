<?php namespace Assetic\Extension\Twig;

class AsseticFilterNode extends \Twig_Node_Expression_Function
{
    protected function compileCallable(\Twig_Compiler $compiler)
    {
        $compiler->raw(sprintf('$this->env->getExtension(\'Assetic\\Extension\\Twig\\AsseticExtension\')->getFilterInvoker(\'%s\')->invoke', $this->getAttribute('name')));

        $this->compileArguments($compiler);
    }
}
