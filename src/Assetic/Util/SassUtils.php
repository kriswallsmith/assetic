<?php namespace Assetic\Util;

/**
 * Sass Utils.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
abstract class SassUtils extends CssUtils
{
    const REGEX_COMMENTS = '/((?:\/\*[^*]*\*+(?:[^\/][^*]*\*+)*\/)|\/\/[^\n]+)/';
}
