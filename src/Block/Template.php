<?php
namespace ViewComponents\Core\Block;

use RuntimeException;
use ViewComponents\Core\ArrayDataPresenterTrait;
use ViewComponents\Core\DataPresenterInterface;
use ViewComponents\Core\Rendering\RendererInterface;
use ViewComponents\Core\Services;
use ViewComponents\Core\AbstractBlock;

class Template extends AbstractBlock implements DataPresenterInterface
{
    use ArrayDataPresenterTrait;

    /** @var  string */
    private $templateName;

    /** @var  RendererInterface */
    private $renderer;

    /**
     * Constructor.
     *
     * @param string|null $templateName
     * @param array|null $data view data
     * @param RendererInterface|null $renderer
     */
    public function __construct($templateName = null, array $data = null, RendererInterface $renderer = null)
    {
        $this->setData($data ?: []);
        $this->templateName = $templateName;
        $this->setRenderer($renderer);
    }

    /**
     * Returns renderer instance used to render template.
     *
     * @return RendererInterface
     */
    public function getRenderer()
    {
        if ($this->renderer === null) {
            $this->renderer = Services::renderer();
        }
        return $this->renderer;
    }

    /**
     * Sets renderer.
     *
     * @param RendererInterface $renderer
     * @return $this
     */
    public function setRenderer(RendererInterface $renderer = null)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Returns template name.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * Sets template.
     *
     * @param string $templateName
     * @return $this
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
        return $this;
    }

    private function getPreparedData()
    {
        $data = $this->getData();
        if (array_key_exists('block', $data)) {
            throw new RuntimeException('Usage of reserved \'block\' key in view data');
        }
        $data['block'] = $this;
        return $data;
    }

    public function renderInternal()
    {
        return $this->getRenderer()->render(
            $this->getTemplateName(),
            $this->getPreparedData()
        );
    }
}
