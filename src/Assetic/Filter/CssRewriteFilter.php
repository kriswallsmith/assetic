<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

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
        $sourceUrl = $asset->getSourceUrl();
        $targetUrl = $asset->getTargetUrl();

        if (null === $sourceUrl || null === $targetUrl || $sourceUrl == $targetUrl) {
            return;
        }

        // learn how to get from the target back to the source
        if (false !== strpos($sourceUrl, '://')) {
            // the source is absolute, this should be easy
            $parts = parse_url($sourceUrl);

            $host = $parts['scheme'].'://'.$parts['host'];
            $path = dirname($parts['path']).'/';
        } else {
            // assume source and target are on the same host
            $host = '';

            // pop entries off the target until it fits in the source
            $path = '';
            $targetDir = dirname($targetUrl);
            while (0 !== strpos($sourceUrl, $targetDir)) {
                if (false !== $pos = strrpos($targetDir, '/')) {
                    $targetDir = substr($targetDir, 0, $pos);
                    $path .= '../';
                } else {
                    throw new \RuntimeException(sprintf('Unable to calculate relative path from "%s" to "%s"', $targetUrl, $sourceUrl));
                }
            }
            $path .= substr(dirname($sourceUrl).'/', strlen($targetDir) + 1);
        }

        $filter = function($url) use($host, $path)
        {
            if (false !== strpos($url, '://')) {
                // absolute
                return $url;
            } elseif ('/' == $url[0]) {
                // root relative
                return $host.$url;
            } else {
                // document relative
                while (0 === strpos($url, '../') && 2 <= substr_count($path, '/')) {
                    $path = substr($path, 0, strrpos(rtrim($path, '/'), '/') + 1);
                    $url = substr($url, 3);
                }
                return $host.$path.$url;
            }
        };

        // tokenize and filter the asset content
        $tokens = $this->tokenizer->tokenizeString($asset->getContent());

        // cleanup the php tags codesniffer adds
        $tokens = array_slice($tokens, 1, -1);
        $token = array_pop($tokens);
        if (' ' != $token['content']) {
            $token['content'] = substr($token['content'], 0, -1);
            $tokens[] = $token;
        }

        $content = '';
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

            $content .= $token['content'];
        }

        $asset->setContent($content);
    }
}
