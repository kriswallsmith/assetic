<?php

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Fixes relative CSS urls.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CssRewriteFilter implements FilterInterface
{
    private $tokenizer;

    public function __construct(\PHP_CodeSniffer_Tokenizers_CSS $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $context = $asset->getContext();
        if (null === $context) {
            return;
        }

        $source = $asset->getUrl();
        $target = $context->getUrl();
        if (null === $source || null === $target || $source == $target) {
            return;
        }

        // todo: compute the difference in urls
        $filter = function($url) use($source, $target)
        {
            return '../'.$url;
        };

        // tokenize and filter the asset body
        $tokens = $this->tokenizer->tokenizeString($asset->getBody());

        // cleanup the php tags codesniffer adds
        $tokens = array_slice($tokens, 1, -1);
        $token = array_pop($tokens);
        if (' ' != $token['content']) {
            $token['content'] = substr($token['content'], 0, -1);
            $tokens[] = $token;
        }

        $code = '';
        $inUrl = $inImport = 0;
        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];

            if (T_URL == $token['code']) {
                $token['content'] = $filter($token['content']);
            } elseif (T_STRING == $token['code'] && 'url' == $token['content']) {
                $inUrl = 1;
            } elseif (T_STRING == $token['code'] && 'import' == $token['content'] && isset($tokens[$i - 1]) && T_ASPERAND == $tokens[$i - 1]['code']) {
                $inImport = 1;
            } elseif (T_OPEN_PARENTHESIS == $token['code'] && 1 == $inUrl) {
                $inUrl = 2;
            } elseif (T_CONSTANT_ENCAPSED_STRING == $token['code'] && (2 == $inUrl || 1 == $inImport)) {
                $quote = $token['content'][0];
                $url = $filter(substr($token['content'], 1, -1));
                $token['content'] = $quote.$url.$quote;
            } elseif (T_WHITESPACE != $token['code']) {
                $inUrl = $inImport = 0;
            }

            $code .= $token['content'];
        }

        $asset->setBody($code);
    }
}
