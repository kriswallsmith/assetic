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
    private $charset;
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
        $pb
            ->inheritEnvironmentVariables()
            ->add($this->javaPath)
            ->add('-jar')
            ->add($this->jarPath)
        ;

        foreach ($options as $option) {
            $pb->add($option);
        }

        if (null !== $this->charset) {
            $pb->add('--charset')->add($this->charset);
        }

        if (null !== $this->lineBreak) {
            $pb->add('--line-break')->add($this->lineBreak);
        }

        // input and output files
        $tempDir = realpath(sys_get_temp_dir());
        $hash = substr(sha1(time().rand(11111, 99999)), 0, 7);
        $input = $tempDir.DIRECTORY_SEPARATOR.$hash.'.'.$type;
        $output = $tempDir.DIRECTORY_SEPARATOR.$hash.'-min.'.$type;
        file_put_contents($input, $content);
        $pb->add('-o')->add($output)->add($input);

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            throw new \RuntimeException($proc->getErrorOutput());
        } elseif (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $retval = file_get_contents($output);
        unlink($output);

        return $retval;
    }
}
