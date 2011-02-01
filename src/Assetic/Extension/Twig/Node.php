<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Twig;

class Node extends \Twig_Node
{
    public function __construct(\Twig_NodeInterface $body, array $sourceUrls, $targetUrl, array $filterNames, $assetName, $debug = false, $lineno = 0, $tag = null)
    {
        $nodes = array('body' => $body);
        $attributes = array(
            'source_urls'  => $sourceUrls,
            'target_url'   => $targetUrl,
            'filter_names' => $filterNames,
            'asset_name'   => $assetName,
            'debug'        => $debug,
        );

        parent::__construct($nodes, $attributes, $lineno, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $body = $this->getNode('body');

        $compiler
            ->addDebugInfo($this)
            ->write("\$context['asset_url'] = ")
            ->subcompile($this->getAssetUrlNode($this->getNode('body')))
            ->raw(";\n")
            ->subcompile($this->getNode('body'))
            ->write("unset(\$context['asset_url']);\n")
        ;
    }

    protected function getAssetUrlNode(\Twig_NodeInterface $body)
    {
        return new \Twig_Node_Expression_Constant($this->getAttribute('target_url'), $body->getLine());
    }
}
