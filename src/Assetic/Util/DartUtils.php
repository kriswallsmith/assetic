<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Util;

/**
 * Dart Utils.
 *
 * @author Pierre du Plessis <pdples@gmail.com>
 */
class DartUtils extends CssUtils
{
    const REGEX_IMPORTS         = '/import? (\'|"|)(?<url>[^\'"\)\n\r]*)\1\)?;?/';
    const REGEX_COMMENTS        = '/((?:\/\*[^*]*\*+(?:[^\/][^*]*\*+)*\/)|\/\/[^\n]+)/';
}
