<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Loader;

/**
 * Loads asset formulae from PHP files.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FunctionCallsFormulaLoader extends BasePhpFormulaLoader
{
    protected function registerPrototypes()
    {
        return array(
            'assetic_assets(*)'      => array(),
            'assetic_javascripts(*)' => array('output' => 'js/*.js'),
            'assetic_stylesheets(*)' => array('output' => 'css/*.css'),
        );
    }

    protected function registerSetupCode()
    {
        return <<<'EOF'
function assetic_assets()
{
    global $_call;
    $_call = func_get_args();
}

function assetic_javascripts()
{
    global $_call;
    $_call = func_get_args();
}

function assetic_stylesheets()
{
    global $_call;
    $_call = func_get_args();
}

EOF;
    }
}
