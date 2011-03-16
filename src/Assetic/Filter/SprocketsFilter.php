<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Runs assets through Sprockets.
 *
 * @link   http://getsprockets.org/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class SprocketsFilter implements FilterInterface
{
    private $baseDir;
    private $sprocketsLib;
    private $rubyBin;
    private $includeDirs = array();
    private $assetRoot;

    public function __construct($baseDir, $sprocketsLib, $rubyBin = '/usr/bin/ruby')
    {
        $this->baseDir = $baseDir;
        $this->sprocketsLib = $sprocketsLib;
        $this->rubyBin = $rubyBin;
    }

    public function addIncludeDir($directory)
    {
        $this->includeDirs[] = $directory;
    }

    public function setAssetRoot($assetRoot)
    {
        $this->assetRoot = $assetRoot;
    }

    /**
     * Hack around a bit, get the job done.
     */
    public function filterLoad(AssetInterface $asset)
    {
        static $format = <<<'EOF'
#!/usr/bin/env ruby

require File.join(%s, 'sprockets')

module Sprockets
  class Secretary
    def reset!(options = @options)
      @options = DEFAULT_OPTIONS.merge(options)
      @environment  = Sprockets::Environment.new(@options[:root])
      @preprocessor = Sprockets::Preprocessor.new(@environment, :strip_comments => @options[:strip_comments])

      add_load_locations(@options[:load_path])
      add_source_files(@options[:source_files])
    end
  end

  class Preprocessor
    protected

    def pathname_for_relative_require_from(source_line)
      Sprockets::Pathname.new(@environment, File.join(%s, location_from(source_line)))
    end
  end
end

options = { :load_path    => [],
            :source_files => [%s],
            :expand_paths => false }

%ssecretary = Sprockets::Secretary.new(options)
secretary.install_assets if options[:asset_root]
print secretary.concatenation

EOF;

        $sourceUrl = $asset->getSourceUrl();
        if (!$sourceUrl || false !== strpos($sourceUrl, '://')) {
            return;
        }

        $more = '';

        foreach ($this->includeDirs as $directory) {
            $more .= 'options[:load_path] << '.var_export($directory, true)."\n";
        }

        if (null !== $this->assetRoot) {
            $more .= 'options[:asset_root] = '.var_export($this->assetRoot, true)."\n";
        }

        if ($more) {
            $more .= "\n";
        }

        $tmpAsset = tempnam(sys_get_temp_dir(), 'assetic_sprockets');
        file_put_contents($tmpAsset, $asset->getContent());

        $input = tempnam(sys_get_temp_dir(), 'assetic_sprockets');
        file_put_contents($input, sprintf($format, var_export($this->sprocketsLib, true), var_export($this->baseDir, true), var_export($tmpAsset, true), $more));

        $proc = new Process($cmd = implode(' ', array_map('escapeshellarg', array($this->rubyBin, $input))));
        $code = $proc->run();
        unlink($tmpAsset);
        unlink($input);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
