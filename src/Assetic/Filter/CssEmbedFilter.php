<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Util\Process;

/**
 * CSSEmbed filter
 *
 * @author Maxime Thirouin <maxime.thirouin@gmail.com>
 */
class CssEmbedFilter implements FilterInterface
{
    private $jarPath;
    private $javaPath;
    private $charset = 'utf-8';
    private $mhtml; // Enable MHTML mode.
    private $mhtmlRoot; // Use <root> as the MHTML root for the file.
    private $root; // Prepends <root> to all relative URLs.
    private $skipMissing; // Don't throw an error for missing image files.
    private $maxUriLength; // Maximum length for a data URI. Defaults to 32768.
    private $maxImageSize; // Maximum image size (in bytes) to convert.

    public function __construct($jarPath, $javaPath = '/usr/bin/java')
    {
        $this->jarPath = $jarPath;
        $this->javaPath = $javaPath;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function setMhtml($mhtml)
    {
        $this->mhtml = $mhtml;
    }

    public function setMhtmlRoot($mhtmlRoot)
    {
        $this->mhtmlRoot = $mhtmlRoot;
    }

    public function setRoot($root)
    {
        $this->root = $root;
    }

    public function setSkipMissing($skipMissing)
    {
        $this->skipMissing = $skipMissing;
    }

    public function setMaxUriLength($maxUriLength)
    {
        $this->maxUriLength = $maxUriLength;
    }

    public function setMaxImageSize($maxImageSize)
    {
        $this->maxImageSize = $maxImageSize;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $options = array(
            $this->javaPath,
            '-jar',
            $this->jarPath,
        );

        if (null !== $this->charset) {
            $options[] = '--charset';
            $options[] = $this->charset;
        }

        if ($this->mhtml) {
            $options[] = '--mhtml';
        }

        if (null !== $this->mhtmlRoot) {
            $options[] = '--mhtmlroot';
            $options[] = $this->mhtmlRoot;
        }

        // automatically define root if not already defined
        if (null === $this->root) {
            $root = $asset->getSourceRoot();
            $path = $asset->getSourcePath();

            if ($root && $path) {
                $options[] = '--root';
                $options[] = dirname($root.'/'.$path);
            }
        } else {
            $options[] = '--root';
            $options[] = $this->root;
        }

        if ($this->skipMissing) {
            $options[] = '--skip-missing';
        }

        if (null !== $this->maxUriLength) {
            $options[] = '--max-uri-length';
            $options[] = $this->maxUriLength;
        }

        if (null !== $this->maxImageSize) {
            $options[] = '--max-image-size';
            $options[] = $this->maxImageSize;
        }

        // input
        $options[] = $input = tempnam(sys_get_temp_dir(), 'assetic_cssembed');
        file_put_contents($input, $asset->getContent());

        $proc = new Process(implode(' ', array_map('escapeshellarg', $options)));
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }
}
