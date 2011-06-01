<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter\Yui;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Util\ProcessBuilder;

/**
 * Base YUI compressor filter.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
abstract class BaseCompressorFilter implements FilterInterface
{
    private $jarPath;
    private $javaPath;
    private $charset = 'utf-8';
    private $lineBreak;

    public function __construct($jarPath, $javaPath = '/usr/bin/java')
    {
        $this->jarPath = $jarPath;
        $this->javaPath = $javaPath;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function setLineBreak($lineBreak)
    {
        $this->lineBreak = $lineBreak;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * Compresses a string.
     *
     * @param string $content The content to compress
     * @param string $type    The type of content, either "js" or "css"
     * @param array  $options An indexed array of additional options
     *
     * @return string The compressed content
     */
    protected function compress($content, $type, $options = array())
    {
        $pb = new ProcessBuilder();
        $pb->add($this->javaPath)->add('-jar')->add($this->jarPath)->add('--type')->add($type);
        foreach ($options as $option) {
            $pb->add($option);
        }

        if (null !== $this->charset) {
            $pb->add('--charset')->add($this->charset);
        }

        if (null !== $this->lineBreak) {
            $pb->add('--line-break')->add($this->lineBreak);
        }

        $pb->setInput($content);

        $proc = $pb->getProcess();
        $code = $proc->run();

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        return $proc->getOutput();
    }
}
