<?php

namespace Assetic\Filter\Sass;

use Assetic\Filter\FilterInterface;

/**
 * Loads SCSS files.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class ScssFilter extends SassFilter
{
    public function __construct($sassPath = '/usr/bin/sass')
    {
        parent::__construct($sassPath);
        $this->setScss(true);
    }
}
