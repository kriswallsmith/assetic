<?php

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents an asset loaded from a file.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileAsset extends BaseAsset
{
    static private $knownContentTypes = array(
        'css' => 'text/css',
        'js'  => 'text/javascript',
    );

    private $path;

    /**
     * Registers a new file extension and content type.
     *
     * @param string $extension   A file extension
     * @param string $contentType A content type
     */
    static public function registerContentType($extension, $contentType)
    {
        self::$knownContentTypes[$extension] = $contentType;
    }

    /**
     * Constructor.
     *
     * @param string $path    The absolute path to the asset
     * @param string $url     The asset URL
     * @param array  $filters Filters for the asset
     */
    public function __construct($path, $url = null, $filters = array())
    {
        parent::__construct($filters);

        $this->path = $path;
        $this->setUrl($url);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad(file_get_contents($this->path), $additionalFilter);
    }

    public function getContentType()
    {
        $extension = pathinfo($this->path, PATHINFO_EXTENSION);

        if (isset(self::$knownContentTypes[$extension])) {
            return self::$knownContentTypes[$extension];
        }
    }

    public function getLastModified()
    {
        return filemtime($this->path);
    }
}
