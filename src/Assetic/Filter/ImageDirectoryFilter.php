<?php

namespace Assetic\Filter;
use Assetic\Asset\AssetInterface;

/**
 * Parses URLs in a CSS and move all images to a folder,
 * changing location of them in CSS.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ImageDirectoryFilter extends BaseCssFilter
{
    protected $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $filter = $this;
        $content = $this->filterReferences($asset->getContent(), function ($match) use ($asset, $filter) {
            $url = $match['url'];

            $url = $filter->filterUrl($asset, $url);

            return 'url('.$match[1].$url.$match[3].')';
        });

        $asset->setContent($content);
    }

    public function filterUrl(AssetInterface $asset, $url)
    {
        $sourceBase = $asset->getSourceRoot();
        $sourcePath = $asset->getSourcePath();

        if (false !== strpos($sourceBase, '://')) {
            return $url;
        }

        $targetDir = $sourceBase.'/'.dirname($asset->getTargetPath());
        $image = $this->addImage($targetDir.'/'.$url);
        $targetPath = $sourceBase.'/'.$asset->getTargetPath();
        $sourcePath = $this->directory.'/'.$image;

        $path = '';
        while (0 !== strpos($sourcePath, $targetDir)) {
            if (false !== $pos = strrpos($targetDir, '/')) {
                $targetDir = substr($targetDir, 0, $pos);
                $path .= '../';
            } else {
                $targetDir = '';
                $path .= '../';
                break;
            }
        }
        $path .= ltrim(substr(dirname($sourcePath).'/', strlen($targetDir)), '/');

        return $path.$image;
    }

    private function addImage($url)
    {
        $name = basename($url);

        if (!file_exists($target = $this->directory.'/'.$name)) {
            $this->copy($url, $target);

            return $name;
        }

        $extPos = strrpos($name, '.');
        $prefix = substr($name, 0, $extPos);

        $count = 1;
        do {
            $newName = $prefix.'_'.$count.substr($name, $extPos);
            $count++;
        } while (file_exists($this->directory.'/'.$newName));

        $this->copy($url, $this->directory.'/'.$newName);

        return $newName;
    }

    private function copy($from, $to)
    {
        if (!is_dir($dir = dirname($to))) {
            mkdir($dir, 0777, true);
        }

        copy($from, $to);
    }
}
