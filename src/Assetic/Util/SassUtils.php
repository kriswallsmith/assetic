<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2015 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Util;

/**
 * Sass Utils.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
abstract class SassUtils extends CssUtils
{
    const REGEX_COMMENTS = '/((?:\/\*[^*]*\*+(?:[^\/][^*]*\*+)*\/)|\/\/[^\n]+)/';
}
