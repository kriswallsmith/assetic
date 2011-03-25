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

    /**
     * Constructor.
     *
     * @param AssetFactory $factory The asset factory
     * @param string       $tag     The tag name
     * @param string       $output  The default output string
     * @param Boolean      $debug   The debug mode
     * @param Boolean      $single  Whether to force a single asset
     */
    public function __construct(AssetFactory $factory, $tag, $output = 'assetic/*', $debug = false, $single = false)
    {
        $this->factory = $factory;
        $this->tag     = $tag;
        $this->output  = $output;
        $this->debug   = $debug;
        $this->single  = $single;
    }

    public function parse(\Twig_Token $token)
    {
        $inputs  = array();
        $output  = $this->output;
        $filters = array();
        $name    = null;
        $debug   = $this->debug;

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
                $output = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'name')) {
                // name='core_js'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $name = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'debug')) {
                // debug=true
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $debug = 'true' == $stream->expect(\Twig_Token::NAME_TYPE, array('true', 'false'))->getValue();
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

        if (null === $name) {
            $name = $this->factory->generateAssetName($inputs, $filters);
        }

        $coll = $this->factory->createAsset($inputs, $filters, array(
            'output' => $output,
            'name'   => $name,
            'debug'  => $debug,
        ));

        if (!$debug) {
            return static::createNode($body, $inputs, $coll->getTargetUrl(), $filters, $name, $debug, $token->getLine(), $this->getTag());
        }

        $nodes = array();
        foreach ($coll as $asset) {
            $nodes[] = static::createNode($body, array($asset->getSourceUrl()), $asset->getTargetUrl(), $filters, $name.'_'.count($nodes), $debug, $token->getLine(), $this->getTag());
        }

        return new \Twig_Node($nodes, array(), $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return $this->tag;
    }

    static protected function createNode(\Twig_NodeInterface $body, array $inputs, $targetUrl, array $filters, $name, $debug = false, $lineno = 0, $tag = null)
    {
        return new AsseticNode($body, $inputs, $targetUrl, $filters, $name, $debug, $lineno, $tag);
    }
}
