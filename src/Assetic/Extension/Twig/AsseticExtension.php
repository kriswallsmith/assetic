<?php namespace Assetic\Extension\Twig;

use Assetic\Factory\AssetFactory;
use Assetic\Contracts\ValueSupplierInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AsseticExtension extends AbstractExtension implements GlobalsInterface
{
    protected $factory;
    protected $functions;
    protected $valueSupplier;

    public function __construct(AssetFactory $factory, $functions = [], ValueSupplierInterface $valueSupplier = null)
    {
        $this->factory = $factory;
        $this->functions = [];
        $this->valueSupplier = $valueSupplier;

        foreach ($functions as $function => $options) {
            if (is_integer($function) && is_string($options)) {
                $this->functions[$options] = array('filter' => $options);
            } else {
                $this->functions[$function] = $options + array('filter' => $function);
            }
        }
    }

    public function getTokenParsers()
    {
        return array(
            new AsseticTokenParser($this->factory, 'javascripts', 'js/*.js'),
            new AsseticTokenParser($this->factory, 'stylesheets', 'css/*.css'),
            new AsseticTokenParser($this->factory, 'image', 'images/*', true),
        );
    }

    public function getFunctions()
    {
        $functions = [];
        foreach ($this->functions as $function => $filter) {
            $functions[] = AsseticFilterFunction::make($this, $function);
        }

        return $functions;
    }

    public function getGlobals()
    {
        return array(
            'assetic' => array(
                'debug' => $this->factory->isDebug(),
                'vars'  => null !== $this->valueSupplier ? new ValueContainer($this->valueSupplier) : [],
            ),
        );
    }

    public function getFilterInvoker($function)
    {
        return new AsseticFilterInvoker($this->factory, $this->functions[$function]);
    }

    public function getName()
    {
        return 'assetic';
    }
}
