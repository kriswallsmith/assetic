<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Twig;

class AsseticFilterNode extends \Twig_Node_Expression_Function
{
    protected function compileCallable(\Twig_Compiler $compiler)
    {
        $compiler->raw(sprintf('$this->env->getExtension(\'assetic\')->getFilterInvoker(\'%s\')->invoke', $this->getAttribute('name')));

        $this->compileArguments($compiler);
    }
}
