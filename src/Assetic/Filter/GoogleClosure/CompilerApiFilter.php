<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter\GoogleClosure;

use Assetic\Asset\AssetInterface;

/**
 * Filter for the Google Closure Compiler API.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CompilerApiFilter extends BaseCompilerFilter
{
    public function filterDump(AssetInterface $asset)
    {
        $query = array(
            'js_code'       => $asset->getContent(),
            'output_format' => 'json',
            'output_info'   => 'compiled_code',
        );

        if (null !== $this->compilationLevel) {
            $query['compilation_level'] = $this->compilationLevel;
        }

        if (null !== $this->jsExterns) {
            $query['js_externs'] = $this->jsExterns;
        }

        if (null !== $this->externsUrl) {
            $query['externs_url'] = $this->externsUrl;
        }

        if (null !== $this->excludeDefaultExterns) {
            $query['exclude_default_externs'] = $this->excludeDefaultExterns ? 'true' : 'false';
        }

        if (null !== $this->formatting) {
            $query['formatting'] = $this->formatting;
        }

        if (null !== $this->useClosureLibrary) {
            $query['use_closure_library'] = $this->useClosureLibrary ? 'true' : 'false';
        }

        if (null !== $this->warningLevel) {
            $query['warning_level'] = $this->warningLevel;
        }

        $context = stream_context_create(array('http' => array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($query),
        )));

        $response = file_get_contents('http://closure-compiler.appspot.com/compile', false, $context);
        $data = json_decode($response);

        if (isset($data->serverErrors) && 0 < count($data->serverErrors)) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException(sprintf('The Google Closure Compiler API threw some server errors: '.print_r($data->serverErrors, true)));
            // @codeCoverageIgnoreEnd
        }

        if (isset($data->errors) && 0 < count($data->errors)) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException(sprintf('The Google Closure Compiler API threw some errors: '.print_r($data->errors, true)));
            // @codeCoverageIgnoreEnd
        }

        $asset->setContent($data->compiledCode);
    }
}
