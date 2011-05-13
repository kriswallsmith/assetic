<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/**
 * Represents an asset loaded from a file.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileAsset extends BaseAsset
{
    private $source;

    /**
     * Constructor.
     *
     * @param string $source  An absolute path
     * @param array  $filters An array of filters
     * @param string $base    The base directory
     * @param string $path    The source path
     *
     * @throws InvalidArgumentException If the supplied base doesn't match the source when guessing the path
     */
    public function __construct($source, $filters = array(), $base = null, $path = null)
    {
        if (null === $base) {
            $base = dirname($source);
            if (null === $path) {
                $path = basename($source);
            }
        } elseif (null === $path) {
            if (0 !== strpos($source, $base)) {
                throw new \InvalidArgumentException(sprintf('The source "%s" is not in the base directory "%s"', $source, $base));
            }

            $path = substr($source, strlen($base) + 1);
        }

        $this->source = $source;

        parent::__construct($filters, $base, $path);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad(file_get_contents($this->source), $additionalFilter);
    }

    public function getLastModified()
    {
        return filemtime($this->source);
    }
}
