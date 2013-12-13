<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Factory\AssetFactory;
use Assetic\Util\DartUtils;

/**
 * Compiles Dart into Javascript.
 *
 * @link http://dartlang.org/
 */
class DartFilter extends BaseProcessFilter implements DependencyExtractorInterface
{
    /**
     * @var string
     */
    private $dartBin;

    /**
     * @param string $dartBin
     */
    public function __construct($dartBin = '/usr/bin/dart2js')
    {
        $this->dartBin = $dartBin;
    }

    /**
     * @param  AssetInterface    $asset
     * @throws \RuntimeException
     */
    public function filterLoad(AssetInterface $asset)
    {
        $input  = tempnam(sys_get_temp_dir(), 'assetic_dart');
        $output = tempnam(sys_get_temp_dir(), 'assetic_dart');

        $content = $asset->getContent();

        $sourcePath = $asset->getSourceDirectory();

        $callback = function ($matches) use ($sourcePath, &$content) {
            if (!$matches['url'] || null === $sourcePath || 'dart:' === substr($matches['url'], 0, 5)) {
                return;
            }

            $currentDir = getcwd();

            chdir($sourcePath);
            $importPath = str_replace('\\', '/', realpath($matches['url']));
            chdir($currentDir);

            if ('/' !== substr($importPath, 0, 1)) {
                $importPath = '/' . $importPath;
            }

            $content = str_replace($matches['url'], $importPath, $content);
        };

        DartUtils::filterImports($content, $callback);

        file_put_contents($input, $content);

        $pb = $this->createProcessBuilder()
            ->add($this->dartBin)
            ->add('-o'.$output)
            ->add($input)
        ;

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 !== $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        if (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $asset->setContent(file_get_contents($output));
        unlink($output);
    }

    /**
     * @param  AssetFactory                          $factory
     * @param  string                                $content
     * @param  null                                  $loadPath
     * @return array|\Assetic\Asset\AssetInterface[]
     */
    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        if (null === $loadPath) {
            return array();
        }

        $children = array();

        foreach (DartUtils::extractImports($content) as $reference) {
            if ('dart:' === substr($reference, 0, 5)) {
                // skip imports that is not files
                continue;
            }

            if (file_exists($file = $loadPath.'/'.$reference)) {
                $coll = $factory->createAsset($file, array(), array('root' => $loadPath));

                foreach ($coll as $leaf) {
                    $leaf->ensureFilter($this);
                    $children[] = $leaf;
                    break;
                }
            }
        }

        return $children;
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
