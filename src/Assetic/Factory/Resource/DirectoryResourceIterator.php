<?php namespace Assetic\Factory\Resource;

/**
 * An iterator that converts file objects into file resources.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @access private
 */
class DirectoryResourceIterator extends \RecursiveIteratorIterator
{
    public function current()
    {
        return new FileResource(parent::current()->getPathname());
    }
}
