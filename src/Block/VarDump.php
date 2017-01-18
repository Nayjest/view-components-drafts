<?php

namespace ViewComponents\Core\Block;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use ViewComponents\Core\AbstractBlock;
use ViewComponents\Core\DataPresenterInterface;
use ViewComponents\Core\DataPresenterTrait;

class VarDump extends AbstractBlock implements DataPresenterInterface
{
    use DataPresenterTrait;

    public function __construct($data = null)
    {
        $this->setData($data);
    }

    protected function renderInternal()
    {
        if (!class_exists('Symfony\Component\VarDumper\Cloner\VarCloner')) {
            return var_export($this->getData(), true);
        }
        $cloner = new VarCloner();
        $dumper = ('cli' === PHP_SAPI ? new CliDumper : new HtmlDumper);
        $output = fopen('php://memory', 'r+b');
        $dumper->dump($cloner->cloneVar($this->getData()), $output);
        return stream_get_contents($output, -1, 0);
    }
}
