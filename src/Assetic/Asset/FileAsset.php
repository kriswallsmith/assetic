<?php

namespace Assetic\Asset;

/**
 * Represents an asset loaded from a file.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileAsset extends Asset
{
    private $path;

    /**
     * Constructor.
     *
     * @param string $path    The absolute file system path
     * @param array  $filters Filters for the asset
     */
    public function __construct($path, $filters = array())
    {
        $this->path = $path;
        parent::__construct(null, $filters);
    }

    /** @inheritDoc */
    public function load()
    {
        $this->originalContent = file_get_contents($this->path);
        parent::load();
    }
}
