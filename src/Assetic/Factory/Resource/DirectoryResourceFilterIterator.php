<?php namespace Assetic\Factory\Resource;

/**
 * Filters files by a basename pattern.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @access private
 */
class DirectoryResourceFilterIterator extends \RecursiveFilterIterator
{
    protected $pattern;

    public function __construct(\RecursiveDirectoryIterator $iterator, $pattern = null)
    {
        parent::__construct($iterator);

        $this->pattern = $pattern;
    }

    public function accept()
    {
        $file = $this->current();
        $name = $file->getBasename();

        if ($file->isDir()) {
            return '.' != $name[0];
        }

        return null === $this->pattern || 0 < preg_match($this->pattern, $name);
    }

    public function getChildren()
    {
        return new self(new \RecursiveDirectoryIterator($this->current()->getPathname(), \RecursiveDirectoryIterator::FOLLOW_SYMLINKS), $this->pattern);
    }
}
