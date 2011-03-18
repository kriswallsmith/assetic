<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
