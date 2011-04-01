<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Twig;

class AsseticNode extends \Twig_Node
{
    /**
     * Constructor.
     *
     * The following attributes are required:
     *
     *  * output: The asset output string
     *  * name:   A name of the asset
     *
     * The following attributes are optional:
     *
     *  * debug:    The debug mode
     *  * var_name: The name of the variable to expose to the body node
     *
     * @param Twig_NodeInterface $body       The body node
     * @param array              $inputs     An array of input strings
     * @param array              $filters    An array of filter strings
     * @param array              $attributes An array of attributes
     * @param integer            $lineno     The line number
     * @param string             $tag        The tag name
     */
    public function __construct(\Twig_NodeInterface $body, array $inputs, array $filters, array $attributes, $lineno = 0, $tag = null)
    {
        $nodes = array('body' => $body);

        $attributes = array_replace(
            array('debug' => false, 'var_name' => 'asset_url'),
            $attributes,
            array('inputs' => $inputs, 'filters' => $filters)
        );

        if ($diff = array_diff(array('output', 'name'), array_keys($attributes))) {
            throw new \InvalidArgumentException('AsseticNode requires the following attribute(s): '.implode(', ', $diff));
        }

        parent::__construct($nodes, $attributes, $lineno, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $body = $this->getNode('body');

        $compiler
            ->addDebugInfo($this)
            ->write('$context['.var_export($this->getAttribute('var_name'), true).'] = ')
            ->subcompile($this->getAssetUrlNode($body))
            ->raw(";\n")
            ->subcompile($body)
            ->write("unset(\$context[".var_export($this->getAttribute('var_name'), true)."]);\n")
        ;
    }

    protected function getAssetUrlNode(\Twig_NodeInterface $body)
    {
        return new \Twig_Node_Expression_Constant($this->getAttribute('output'), $body->getLine());
    }
}
