<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Assetic\Factory\AssetFactory;

/**
 * Initializes the global assetic object.
 */
function assetic_init(AssetFactory $factory, $debug = false, $defaultJavascriptsOutput = 'js/*.js', $defaultStylesheetsOutput = 'css/*.css')
{
    global $assetic;

    $assetic = new stdClass();
    $assetic->factory = $factory;
    $assetic->debug = $debug;
    $assetic->defaultJavascriptsOutput = $defaultJavascriptsOutput;
    $assetic->defaultStylesheetsOutput = $defaultStylesheetsOutput;
}

/**
 * Returns an array of asset urls.
 */
function assetic_assets($inputs = array(), $filters = array(), array $options = array())
{
    global $assetic;

    if (!is_array($inputs)) {
        $inputs = array_map('trim', explode(',', $inputs));
    }

    if (!is_array($filters)) {
        $filters = array_map('trim', explode(',', $filters));
    }

    if (!isset($options['debug'])) {
        $options['debug'] = $assetic->debug;
    }

    $coll = $assetic->factory->createAsset($inputs, $filters, $options);
    if (!$options['debug']) {
        return array($coll->getTargetUrl());
    }

    $urls = array();
    foreach ($coll as $leaf) {
        $urls[] = $leaf->getTargetUrl();
    }

    return $urls;
}

/**
 * Returns an array of javascript urls.
 */
function assetic_javascripts($inputs = array(), $filters = array(), array $options = array())
{
    global $assetic;

    if (!isset($options['output'])) {
        $options['output'] = $assetic->defaultJavascriptsOutput;
    }

    return assetic_assets($inputs, $filters, $options);
}

/**
 * Returns an array of stylesheet urls.
 */
function assetic_stylesheets($inputs = array(), $filters = array(), array $options = array())
{
    global $assetic;

    if (!isset($options['output'])) {
        $options['output'] = $assetic->defaultStylesheetsOutput;
    }

    return assetic_assets($inputs, $filters, $options);
}
