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

use Assetic\Factory\AssetFactory;

class AsseticTokenParser extends \Twig_TokenParser
{
    private $factory;
    private $tag;
    private $output;
    private $debug;
    private $single;
    private $extensions;

    /**
     * Constructor.
     *
     * Attributes can be added to the tag by passing names as the options
     * array. These values, if found, will be passed to the factory and node.
     *
     * @param AssetFactory $factory    The asset factory
     * @param string       $tag        The tag name
     * @param string       $output     The default output string
     * @param Boolean      $debug      The debug mode
     * @param Boolean      $single     Whether to force a single asset
     * @param array        $extensions Additional attribute names to look for
     */
    public function __construct(AssetFactory $factory, $tag, $output, $debug = false, $single = false, array $extensions = array())
    {
        $this->factory    = $factory;
        $this->tag        = $tag;
        $this->output     = $output;
        $this->debug      = $debug;
        $this->single     = $single;
        $this->extensions = $extensions;
    }

    public function parse(\Twig_Token $token)
    {
        $inputs = array();
        $filters = array();
        $attributes = array(
            'name'     => null,
            'output'   => $this->output,
            'debug'    => $this->debug,
            'var_name' => 'asset_url',
        );

        $stream = $this->parser->getStream();
        while (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test(\Twig_Token::STRING_TYPE)) {
                // '@jquery', 'js/src/core/*', 'js/src/extra.js'
                $inputs[] = $stream->next()->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'filter')) {
                // filter='yui_js'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $filters = array_merge($filters, array_filter(array_map('trim', explode(',', $stream->expect(\Twig_Token::STRING_TYPE)->getValue()))));
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'output')) {
                // output='js/packed/*.js' OR output='js/core.js'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes['output'] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'name')) {
                // name='core_js'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes['name'] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'as')) {
                // as='the_url'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes['var_name'] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'debug')) {
                // debug=true
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes['debug'] = 'true' == $stream->expect(\Twig_Token::NAME_TYPE, array('true', 'false'))->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, $this->extensions)) {
                // an arbitrary configured attribute
                $key = $stream->next()->getValue();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes[$key] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } else {
                $token = $stream->getCurrent();
                throw new \Twig_Error_Syntax(sprintf('Unexpected token "%s" of value "%s"', \Twig_Token::typeToEnglish($token->getType(), $token->getLine()), $token->getValue()), $token->getLine());
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $endtag = 'end'.$this->getTag();
        $test = function(\Twig_Token $token) use($endtag) { return $token->test($endtag); };
        $body = $this->parser->subparse($test, true);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        if ($this->single && 1 < count($inputs)) {
            $inputs = array_slice($inputs, -1);
        }

        if (!isset($attributes['name'])) {
            $attributes['name'] = $this->factory->generateAssetName($inputs, $filters);
        }

        $coll = $this->factory->createAsset($inputs, $filters, $attributes);

        if (!$attributes['debug']) {
            return static::createNode($body, $inputs, $filters, array_replace($attributes, array('output' => $coll->getTargetUrl())), $token->getLine(), $this->getTag());
        }

        $nodes = array();
        foreach ($coll as $leaf) {
            $nodes[] = static::createNode($body, array($leaf->getSourceUrl()), $filters, array_replace($attributes, array('output' => $leaf->getTargetUrl(), 'name' => $attributes['name'].'_'.count($nodes))), $token->getLine(), $this->getTag());
        }

        return new \Twig_Node($nodes, array(), $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return $this->tag;
    }

    static protected function createNode(\Twig_NodeInterface $body, array $inputs, array $filters, array $attributes, $lineno = 0, $tag = null)
    {
        return new AsseticNode($body, $inputs, $filters, $attributes, $lineno, $tag);
    }
}
