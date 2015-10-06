<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Util\Analyzer;

/**
 * LESS analyzer
 *
 * @author Alex Ash <streamprop@gmail.com>
 */
class LessAnalyzer extends CssAnalyzer
{
    protected $general = '/^(?<processed>.*?\R*)(?<unprocessed>(?<token>\'|"|\/\*|\/\/)(.*))?$/su';
    protected $comment = array(
        array(
            'begin'  => '/*',
            'end'    => '*/',
            'regexp' => '/^(?<processed>\/\*.*?(?<token>\*\/))(?<unprocessed>.*)$/su',
        ),
        array(
            'begin'  => '//',
            'end'    => '',
            'regexp' => '/^(?<processed>\/\/.*?)(?<token>.{0})(?<unprocessed>\R.*)$/su',
        ),
    );
}
