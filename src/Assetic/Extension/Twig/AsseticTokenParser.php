<?php namespace Assetic\Extension\Twig;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;
use Twig\Node\Node;
use Twig\Error\SyntaxError;

class AsseticTokenParser extends AbstractTokenParser
{
    private $factory;
    private $tag;
    private $output;
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
     * @param Boolean      $single     Whether to force a single asset
     * @param array        $extensions Additional attribute names to look for
     */
    public function __construct(AssetFactory $factory, $tag, $output, $single = false, array $extensions = [])
    {
        $this->factory    = $factory;
        $this->tag        = $tag;
        $this->output     = $output;
        $this->single     = $single;
        $this->extensions = $extensions;
    }

    public function parse(Token $token)
    {
        $inputs = [];
        $filters = [];
        $name = null;
        $attributes = array(
            'output'   => $this->output,
            'var_name' => 'asset_url',
            'vars'     => [],
        );

        $stream = $this->parser->getStream();
        while (!$stream->test(Token::BLOCK_END_TYPE)) {
            if ($stream->test(Token::STRING_TYPE)) {
                // '@jquery', 'js/src/core/*', 'js/src/extra.js'
                $inputs[] = $stream->next()->getValue();
            } elseif ($stream->test(Token::NAME_TYPE, 'filter')) {
                // filter='yui_js'
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $filters = array_merge($filters, array_filter(array_map('trim', explode(',', $stream->expect(Token::STRING_TYPE)->getValue()))));
            } elseif ($stream->test(Token::NAME_TYPE, 'output')) {
                // output='js/packed/*.js' OR output='js/core.js'
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $attributes['output'] = $stream->expect(Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(Token::NAME_TYPE, 'name')) {
                // name='core_js'
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $name = $stream->expect(Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(Token::NAME_TYPE, 'as')) {
                // as='the_url'
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $attributes['var_name'] = $stream->expect(Token::STRING_TYPE)->getValue();
            } elseif ($stream->test(Token::NAME_TYPE, 'debug')) {
                // debug=true
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $attributes['debug'] = 'true' == $stream->expect(Token::NAME_TYPE, array('true', 'false'))->getValue();
            } elseif ($stream->test(Token::NAME_TYPE, 'combine')) {
                // combine=true
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $attributes['combine'] = 'true' == $stream->expect(Token::NAME_TYPE, array('true', 'false'))->getValue();
            } elseif ($stream->test(Token::NAME_TYPE, 'vars')) {
                // vars=['locale','browser']
                $stream->next();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $stream->expect(Token::PUNCTUATION_TYPE, '[');

                while ($stream->test(Token::STRING_TYPE)) {
                    $attributes['vars'][] = $stream->expect(Token::STRING_TYPE)->getValue();

                    if (!$stream->test(Token::PUNCTUATION_TYPE, ',')) {
                        break;
                    }

                    $stream->next();
                }

                $stream->expect(Token::PUNCTUATION_TYPE, ']');
            } elseif ($stream->test(Token::NAME_TYPE, $this->extensions)) {
                // an arbitrary configured attribute
                $key = $stream->next()->getValue();
                $stream->expect(Token::OPERATOR_TYPE, '=');
                $attributes[$key] = $stream->expect(Token::STRING_TYPE)->getValue();
            } else {
                $token = $stream->getCurrent();
                throw new SyntaxError(
                    sprintf(
                        'Unexpected token "%s" of value "%s"',
                        Token::typeToEnglish($token->getType()),
                        $token->getValue()
                    ),
                    $token->getLine(),
                    $stream->getSourceContext()->getName()
                );
            }
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'testEndTag'), true);

        $stream->expect(Token::BLOCK_END_TYPE);

        if ($this->single && 1 < count($inputs)) {
            $inputs = array_slice($inputs, -1);
        }

        if (!$name) {
            $name = $this->factory->generateAssetName($inputs, $filters, $attributes);
        }

        $asset = $this->factory->createAsset($inputs, $filters, $attributes + array('name' => $name));

        return $this->createBodyNode($asset, $body, $inputs, $filters, $name, $attributes, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function testEndTag(Token $token)
    {
        return $token->test(array('end'.$this->getTag()));
    }

    /**
     * @param AssetInterface $asset
     * @param Node $body
     * @param array $inputs
     * @param array $filters
     * @param string $name
     * @param array $attributes
     * @param int $lineno
     * @param string $tag
     *
     * @return Node
     * @throws \ReflectionException
     */
    protected function createBodyNode(AssetInterface $asset, Node $body, array $inputs, array $filters, $name, array $attributes = [], $lineno = 0, $tag = null)
    {
        $reflector = new \ReflectionMethod($this, 'createNode');

        if (__CLASS__ !== $reflector->getDeclaringClass()->name) {
            @trigger_error(sprintf('Overwriting %s::createNode is deprecated since 1.3. Overwrite %s instead.', __CLASS__, __METHOD__), E_USER_DEPRECATED);

            return $this->createNode($asset, $body, $inputs, $filters, $name, $attributes, $lineno, $tag);
        }

        return new AsseticNode($asset, $body, $inputs, $filters, $name, $attributes, $lineno, $tag);
    }

    /**
     * @param AssetInterface      $asset
     * @param Node                $body
     * @param array               $inputs
     * @param array               $filters
     * @param string              $name
     * @param array               $attributes
     * @param int                 $lineno
     * @param string              $tag
     *
     * @return Node
     *
     * @deprecated since 1.3.0, to be removed in 2.0. Use createBodyNode instead.
     */
    protected function createNode(AssetInterface $asset, Node $body, array $inputs, array $filters, $name, array $attributes = [], $lineno = 0, $tag = null)
    {
        @trigger_error(sprintf('The %s method is deprecated since 1.3 and will be removed in 2.0. Use createBodyNode instead.', __METHOD__), E_USER_DEPRECATED);

        if (!$body instanceof Node) {
            throw new \InvalidArgumentException('The body must be a Twig\Node\Node. Custom implementations of Node are not supported.');
        }

        return new AsseticNode($asset, $body, $inputs, $filters, $name, $attributes, $lineno, $tag);
    }
}
