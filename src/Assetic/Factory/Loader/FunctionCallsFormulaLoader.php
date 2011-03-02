<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Loader;

use Assetic\Factory\AssetFactory;
use Assetic\Factory\Resource\ResourceInterface;

/**
 * Loads asset formulae from calls to Assetic functions.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FunctionCallsFormulaLoader implements FormulaLoaderInterface
{
    protected $factory;

    public function __construct(AssetFactory $factory)
    {
        $this->factory = $factory;
    }

    public function load(ResourceInterface $resource)
    {
        // tokenize the prototype and remove the leading php tag
        $prototype = token_get_all('<?php assetic_assets(*)');
        array_shift($prototype);

        $inWildcard = false;
        $buffer = '';
        $level = 0;
        $calls = array();

        $tokens = token_get_all($resource->getContent());
        while ($token = array_shift($tokens)) {
            $current = self::tokenToString($token);
            if ($inWildcard) {
                switch ($current) {
                    case '(': ++$level; break;
                    case ')': --$level; break;
                }

                $buffer .= $current;

                if (!$level) {
                    $calls[] = $buffer.';';
                    $buffer = '';
                    $inWildcard = false;
                }
            } elseif ($current == self::tokenToString(current($prototype))) {
                $buffer .= $current;
                if ('*' == self::tokenToString(next($prototype))) {
                    $inWildcard = true;
                    ++$level;
                }
            } else {
                reset($prototype);
                $buffer = '';
                $inWildcard = false;
            }
        }

        $formulae = array();
        foreach ($calls as $call) {
            $formulae += $this->processCall($call);
        }

        return $formulae;
    }

    private function processCall($call)
    {
        $code = implode("\n", array(
            '$call = array();',
            $this->getSetupCode(),
            $call,
            'var_export($call);'
        ));

        $args = shell_exec(implode(' ', array_map('escapeshellarg', array('php', '-r', $code))));
        $args = eval('return '.$args.';');

        $inputs  = isset($args[0]) ? self::argumentToArray($args[0]) : array();
        $filters = isset($args[1]) ? self::argumentToArray($args[1]) : array();
        $options = isset($args[2]) ? $args[2] : array();

        $output = isset($options['output']) ? $options['output'] : null;
        $name   = isset($options['name']) ? $options['name'] : $this->factory->generateAssetName($inputs, $filters);
        $debug  = isset($options['debug']) ? $options['debug'] : false;

        $coll = $this->factory->createAsset($inputs, $filters, array(
            'output' => $output,
            'name'   => $name,
            'debug'  => $debug,
        ));

        if (!$this->factory->isDebug()) {
            return array($name => array($inputs, $filters, $options));
        }

        $formulae = array();
        foreach ($coll as $asset) {
            $formulae[$name.'_'.count($formulae)] = array(
                array($asset->getSourceUrl()),
                $filters,
                array(
                    'output' => '*',
                    'name'   => $name.'_'.count($formulae),
                    'debug'  => $debug,
                )
            );
        }

        return $formulae;
    }

    protected function getSetupCode()
    {
        return <<<EOF
function assetic_assets()
{
    global \$call;
    \$call = func_get_args();
}

EOF;
    }

    static protected function tokenToString($token)
    {
        return is_array($token) ? $token[1] : $token;
    }

    static protected function argumentToArray($argument)
    {
        return is_array($argument) ? $argument : array_filter(array_map('trim', explode(',', $argument)));
    }
}
