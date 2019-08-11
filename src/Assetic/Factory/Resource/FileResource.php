<?php namespace Assetic\Factory\Resource;

use Assetic\Contracts\Factory\Resource\ResourceInterface;

/**
 * A resource is something formulae can be loaded from.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileResource implements ResourceInterface
{
    private $path;

    /**
     * Constructor.
     *
     * @param string $path The path to a file
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function isFresh($timestamp)
    {
        return file_exists($this->path) && filemtime($this->path) <= $timestamp;
    }

    public function getContent()
    {
        return file_exists($this->path) ? file_get_contents($this->path) : '';
    }

    public function __toString()
    {
        return $this->path;
    }
}
