<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Resource\Util;

/**
 * Filters a recursive directory iterator by file name.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class DirectoryFilterIterator extends \FilterIterator
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * Creates a new filter iterator based on a recursive directory iterator.
     *
     * @param \Iterator $iterator The inner iterator
     * @param string    $pattern  A regular expression
     *
     * @throws \InvalidArgumentException If the pattern is not a string
     */
    public function __construct(\Iterator $iterator, $pattern)
    {
        parent::__construct($iterator);

        if (!is_string($pattern)) {
            throw new \InvalidArgumentException('The pattern should be a string');
        }

        $this->pattern = $pattern;
    }

    public function accept()
    {
        return preg_match($this->pattern, $this->current()->getBasename());
    }
}
