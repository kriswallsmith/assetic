<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * Compiles TypeScript into JavaScript.
 *
 * @link http://www.typescriptlang.org/
 * @author Jarrod Nettles <jarrod.nettles@icloud.com>
 */
class TypeScriptFilter extends BaseNodeFilter
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath = '/usr/bin/tsc';

    /**
     * {@inheritDoc}
     */
    protected function getInputPath(string $input)
    {
        $prefix = preg_replace('/[^\w]/', '', static::class);
        $path = FilesystemUtils::createThrowAwayDirectory($prefix) . '/input.ts';
        file_put_contents($path, $input);
        return $path;
    }

    /**
     * {@inheritDoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $args = [
            '{INPUT}',
            '--out',
            '{OUTPUT}'
        ];

        $result = $this->runProcess($asset->getContent(), $args);
        $asset->setContent($result);
    }
}
