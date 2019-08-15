<?php namespace Assetic\Filter;

use Assetic\Util\FilesystemUtils;
use Assetic\Contracts\Asset\AssetInterface;

/**
 * Runs assets through Packager.
 *
 * @link https://github.com/kamicane/packager
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class PackagerFilter extends BaseFilter
{
    private $packages;

    public function __construct(array $packages = [])
    {
        $this->packages = $packages;
    }

    public function addPackage($package)
    {
        $this->packages[] = $package;
    }

    public function filterLoad(AssetInterface $asset)
    {
        static $manifest = <<<EOF
name: Application%s
sources: [source.js]

EOF;

        $hash = substr(sha1(time().rand(11111, 99999)), 0, 7);
        $package = FilesystemUtils::getTemporaryDirectory().'/assetic_packager_'.$hash;

        mkdir($package);
        file_put_contents($package.'/package.yml', sprintf($manifest, $hash));
        file_put_contents($package.'/source.js', $asset->getContent());

        $packager = new \Packager(array_merge(array($package), $this->packages));
        $content = $packager->build([], [], array('Application'.$hash));

        unlink($package.'/package.yml');
        unlink($package.'/source.js');
        rmdir($package);

        $asset->setContent($content);
    }
}
