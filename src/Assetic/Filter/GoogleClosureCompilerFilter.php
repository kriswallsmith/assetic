<?php

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Buzz\Browser;
use Buzz\Message\Request;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Wraps the Google closure compiler API.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class GoogleClosureCompilerFilter implements FilterInterface
{
    // compilation levels
    const COMPILE_WHITESPACE_ONLY = 'WHITESPACE_ONLY';
    const COMPILE_SIMPLE_OPTIMIZATIONS = 'SIMPLE_OPTIMIZATIONS';
    const COMPILE_ADVANCED_OPTIMIZATIONS = 'ADVANCED_OPTIMIZATIONS';

    // formatting modes
    const FORMAT_PRETTY_PRINT = 'pretty_print';
    const FORMAT_PRINT_INPUT_DELIMITER = 'print_input_delimiter';

    // warning levels
    const LEVEL_QUIET = 'QUIET';
    const LEVEL_DEFAULT = 'DEFAULT';
    const LEVEL_VERBOSE = 'VERBOSE';

    private $browser;

    private $compilationLevel;
    private $jsExterns;
    private $externsUrl;
    private $excludeDefaultExterns;
    private $formatting;
    private $useClosureLibrary;
    private $warningLevel;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    public function setCompilationLevel($compilationLevel)
    {
        $this->compilationLevel = $compilationLevel;
    }

    public function setJsExterns($jsExterns)
    {
        $this->jsExterns = $jsExterns;
    }

    public function setExternsUrl($externsUrl)
    {
        $this->externsUrl = $externsUrl;
    }

    public function setExcludeDefaultExterns($excludeDefaultExterns)
    {
        $this->excludeDefaultExterns = $excludeDefaultExterns;
    }

    public function setFormatting($formatting)
    {
        $this->formatting = $formatting;
    }

    public function setUseClosureLibrary($useClosureLibrary)
    {
        $this->useClosureLibrary = $useClosureLibrary;
    }

    public function setWarningLevel($warningLevel)
    {
        $this->warningLevel = $warningLevel;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $query = array(
            'js_code'       => $asset->getBody(),
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

        $request = new Request('POST', '/compile', 'http://closure-compiler.appspot.com');
        $request->addHeader('Content-Type: application/x-www-form-urlencoded');
        $request->setContent(http_build_query($query));

        $response = $this->browser->send($request);
        $data = json_decode($response->getContent());

        if (isset($data->serverErrors) && 0 < count($data->serverErrors)) {
            throw new \RuntimeException(sprintf('The Google Closure Compiler API threw some server errors: '.print_r($data->serverErrors, true)));
        }

        if (isset($data->errors) && 0 < count($data->errors)) {
            throw new \RuntimeException(sprintf('The Google Closure Compiler API threw some errors: '.print_r($data->errors, true)));
        }

        $asset->setBody($data->compiledCode);
    }
}
