<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\DependencyExtractorInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Util\CssUtils;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\ScssPhp\ValueConverter;

/**
 * Loads SCSS files using the PHP implementation of scss, scssphp.
 *
 * Scss files are mostly compatible, but there are slight differences.
 *
 * @link http://leafo.net/scssphp/
 *
 * @author Bart van den Burg <bart@samson-it.nl>
 */
class ScssphpFilter extends BaseFilter implements DependencyExtractorInterface
{
    private $importPaths = [];
    private $customFunctions = [];
    private $formatter;
    private $outputStyle;
    private $variables = [];

    public function enableCompass($enable = true)
    {
        trigger_deprecation(
            'assetic/framework',
            '2.0.0',
            'Compass for scssphp is deprecated and no longer supported.'
        );
    }

    public function isCompassEnabled()
    {
        trigger_deprecation(
            'assetic/framework',
            '2.0.0',
            'Compass for scssphp is deprecated and no longer supported.'
        );

        return false;
    }

    public function setFormatter($formatter)
    {
        trigger_deprecation(
            'scssphp/scssphp',
            '1.4.0',
            'The method "%s" is deprecated. Use "%s" instead.',
            'setFormatter',
            'setOutputStyle'
        );

        $legacyFormatters = array(
            'scss_formatter' => 'ScssPhp\ScssPhp\Formatter\Expanded',
            'scss_formatter_nested' => 'ScssPhp\ScssPhp\Formatter\Nested',
            'scss_formatter_compressed' => 'ScssPhp\ScssPhp\Formatter\Compressed',
            'scss_formatter_crunched' => 'ScssPhp\ScssPhp\Formatter\Crunched',
        );

        if (isset($legacyFormatters[$formatter])) {
            @trigger_error(sprintf('The scssphp formatter `%s` is deprecated. Use `%s` instead.', $formatter, $legacyFormatters[$formatter]), E_USER_DEPRECATED);

            $formatter = $legacyFormatters[$formatter];
        }

        $this->formatter = $formatter;
    }

    public function setOutputStyle(string $outputStyle)
    {
        if (!in_array($outputStyle, [OutputStyle::EXPANDED, OutputStyle::COMPRESSED])) {
            throw new \InvalidArgumentException('The output style must be compatible with `ScssPhp\ScssPhp\OutputStyle`');
        }

        $this->outputStyle = $outputStyle;
    }

    public function setVariables(array $variables)
    {
        $this->variables = [];

        foreach ($variables as $name => $value) {
            $this->variables[$name] = ValueConverter::parseValue($value);
        }
    }

    public function addVariable($variable)
    {
        $this->variables[] = $variable;
    }

    public function setImportPaths(array $paths)
    {
        $this->importPaths = $paths;
    }

    public function addImportPath($path)
    {
        $this->importPaths[] = $path;
    }

    public function registerFunction($name, $callable, array $argumentDeclaration = null)
    {
        $this->customFunctions[$name] = [
            'callable' => $callable,
            'argumentDeclaration' => $argumentDeclaration,
        ];
    }

    public function filterLoad(AssetInterface $asset)
    {
        $sc = new Compiler();

        if ($dir = $asset->getSourceDirectory()) {
            $sc->addImportPath($dir);
        }

        foreach ($this->importPaths as $path) {
            $sc->addImportPath($path);
        }

        foreach ($this->customFunctions as $name => $function) {
            $sc->registerFunction($name, $function['callable'], $function['argumentDeclaration']);
        }

        if ($this->formatter) {
            $sc->setFormatter($this->formatter);
        }

        if ($this->outputStyle) {
            $sc->setOutputStyle($this->outputStyle);
        }

        if (!empty($this->variables)) {
            $sc->addVariables($this->variables);
        }

        $asset->setContent($sc->compileString($asset->getContent())->getCss());
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        $sc = new Compiler();
        if ($loadPath !== null) {
            $sc->addImportPath($loadPath);
        }

        foreach ($this->importPaths as $path) {
            $sc->addImportPath($path);
        }

        $children = [];
        foreach (CssUtils::extractImports($content) as $match) {
            $file = $sc->findImport($match);
            if ($file) {
                $children[] = $child = $factory->createAsset($file, [], ['root' => $loadPath]);
                $child->load();
                $childLoadPath = $child->all()[0]->getSourceDirectory();
                $children = array_merge($children, $this->getChildren($factory, $child->getContent(), $childLoadPath));
            }
        }

        return $children;
    }
}
