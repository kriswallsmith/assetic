<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Factory\AssetFactory;
use Assetic\Util\FilesystemUtils;
use Assetic\Contracts\Filter\DependencyExtractorInterface;

/**
 * Loads STYL files.
 *
 * @link http://learnboost.github.com/stylus/
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class StylusFilter extends BaseNodeFilter implements DependencyExtractorInterface
{
    private $nodeBin;
    private $compress;
    private $useNib;

    /**
     * Constructs filter.
     *
     * @param string $nodeBin   The path to the node binary
     * @param array  $nodePaths An array of node paths
     */
    public function __construct($nodeBin = '/usr/bin/node', array $nodePaths = [])
    {
        $this->nodeBin = $nodeBin;
        $this->setNodePaths($nodePaths);
    }

    /**
     * Enable output compression.
     *
     * @param boolean $compress
     */
    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    /**
     * Enable the use of Nib
     *
     * @param boolean $useNib
     */
    public function setUseNib($useNib)
    {
        $this->useNib = $useNib;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        static $format = <<<'EOF'
var stylus = require('stylus');
var sys    = require(process.binding('natives').util ? 'util' : 'sys');

stylus(%s, %s)%s.render(function(e, css){
    if (e) {
        throw e;
    }

    sys.print(css);
    process.exit(0);
});

EOF;

        // parser options
        $parserOptions = [];
        if ($dir = $asset->getSourceDirectory()) {
            $parserOptions['paths'] = array($dir);
            $parserOptions['filename'] = basename($asset->getSourcePath());
        }

        if (null !== $this->compress) {
            $parserOptions['compress'] = $this->compress;
        }

        $input = FilesystemUtils::createTemporaryFile('stylus', sprintf($format,
            json_encode($asset->getContent()),
            json_encode($parserOptions),
            $this->useNib ? '.use(require(\'nib\')())' : ''
        ));

        $process = $this->createProcess([$this->nodeBin, $input]);

        $code = $process->run();
        unlink($input);

        if (0 !== $code) {
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        $asset->setContent($process->getOutput());
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        // todo
        return [];
    }
}
