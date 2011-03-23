<?php

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\Sass\SassFilter;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Loads Compass files.
 *
 * @see http://beta.compass-style.org/help/tutorials/command-line/
 * @see https://github.com/miracle2k/webassets/blob/master/src/webassets/filter/compass.py
 * @author Maxime Thirouin <dev@moox.fr>
 */
class CompassFilter extends SassFilter
{
    public function __construct($compassPath = '/usr/bin/compass', $sassPath = '/usr/bin/sass')
    {
        parent::__construct($sassPath, $compassPath);

        // Compass does not allow us to add import path in command line
        // but we can do this with sass, with the new option --compass
        // @see http://groups.google.com/group/compass-users/browse_thread/thread/a476dfcd2b47653e
        $this->setCompass(true);
    }
}
