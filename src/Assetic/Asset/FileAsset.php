<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;
use Assetic\Util\VarUtils;

/**
 * Represents an asset loaded from a file.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileAsset extends BaseAsset
{
    private $source;

    /**
     * Constructor.
     *
     * @param string $source     An absolute path
     * @param array  $filters    An array of filters
     * @param string $sourceRoot The source asset root directory
     * @param string $sourcePath The source asset path
     * @param array  $vars
     *
     * @throws \InvalidArgumentException If the supplied root doesn't match the source when guessing the path
     */
    public function __construct($source, $filters = array(), $sourceRoot = null, $sourcePath = null, array $vars = array())
    {
        if (null === $sourceRoot) {
            $sourceRoot = dirname($source);
            if (null === $sourcePath) {
                $sourcePath = basename($source);
            }
        } elseif (null === $sourcePath) {
            if (0 !== strpos($source, $sourceRoot)) {
                throw new \InvalidArgumentException(sprintf('The source "%s" is not in the root directory "%s"', $source, $sourceRoot));
            }

            $sourcePath = substr($source, strlen($sourceRoot) + 1);
        }

        $this->source = $source;

        parent::__construct($filters, $sourceRoot, $sourcePath, $vars);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $source = VarUtils::resolve($this->source, $this->getVars(), $this->getValues());

        if (!is_file($source)) {
            throw new \RuntimeException(sprintf('The source file "%s" does not exist.', $source));
        }

        $this->doLoad(file_get_contents($source), $additionalFilter);
    }

    public function getLastModified()
    {
        $source = VarUtils::resolve($this->source, $this->getVars(), $this->getValues());

        if (!is_file($source)) {
            throw new \RuntimeException(sprintf('The source file "%s" does not exist.', $source));
        }

        if (strpos($source, '.less')!== false) {
            $sourceTime = filemtime($source);
            $fileTime =  self::checkChildrenLess($source, array('time'=>$sourceTime));

            if   ($sourceTime< $fileTime['time'] ) {
                touch($source, $fileTime['time'] );
            }
        }
        
        return filemtime($source);
    }
        /**
     *
     *
     * @param string    $source       Complete file path + Name
     * @param array     $maxTime      Array with the name that cause the touch on the master file
     * @param string    $extension    Extension in order to allow .less . sass
     * @return array
     * @author Yoni Alhadeff
     */
    public static function checkChildrenLess ($source, $maxTime, $extension = '.less')
    {
        $path = pathinfo($source);
        $dirName = $path['dirname'];
        $f = fopen($source, 'r');

        $lineNo = 0;
        $maxLine = 50; // after 50 lines I won't check anymore if we have imports
        $containImport = false;

        while ($line = fgets($f)) {

            if ($lineNo<10  && strpos($line,'@start-import')) { // If I don't have start statement in the first 10 lines
                $containImport = true;
            }
            //If I didn't have a start statement for imports, in the first lines, I won't read all the file
            if ($lineNo>10 && $containImport == false) {
                break;
            }
            if (strpos($line,'@end-import')) { //If I detect end import statement, I can stop
                break;
            }
            if ($lineNo > $maxLine) {
                break;
            }
            //Looking for any @import statement
            if (strpos($line,'@import')!==false) {
                if (preg_match('/"([^"]+)"/', $line , $m)) {
                    $importedFileName = $m[1];
                    if (strpos($importedFileName,$extension)===false) {
                        $importedFileName .=$extension;
                    }

                    $importedFileFullPath = $dirName.'/'.$importedFileName;
                    $newFileTime = filemtime($importedFileFullPath);

                    if ($newFileTime>$maxTime['time']) {
                        $maxTime=  array("file"=>$importedFileFullPath,'time'=>$newFileTime);
                        return $maxTime; //If the parent has been changed to need to curse
                    }
                    $maxTime =  self::checkChildrenLess($importedFileFullPath, $maxTime, $extension);
                }

            }
            $lineNo++;
        }
        return $maxTime;
    }
}
