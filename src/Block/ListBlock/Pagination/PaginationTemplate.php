<?php

namespace ViewComponents\Core\Block\ListBlock\Pagination;

use ViewComponents\Core\Block\ListBlock\Pagination;
use ViewComponents\Core\Block\Template;
use ViewComponents\Core\Common\UriFunctions;
use ViewComponents\Core\Rendering\RendererInterface;


class PaginationTemplate extends Template implements PaginationViewInterface
{
    /** @var string */
    private $linkTemplateName;

    public function __construct(
        $templateName = 'pagination',
        $linkTemplateName = 'pagination/link',
        RendererInterface $renderer = null
    ) {
        parent::__construct($templateName, [], $renderer);
        $this->linkTemplateName = $linkTemplateName;
    }

    /**
     * @return string
     */
    public function getLinkTemplateName()
    {
        return $this->linkTemplateName;
    }

    public function setCurrent($value)
    {
        $this->setDataItem('current', $value);
        return $this;
    }

    public function setTotal($value)
    {
        $this->setDataItem('total', $value);
        return $this;
    }

    public function setUriKey($value)
    {
        $this->setDataItem('uriKey', $value);
        return $this;
    }

    /**
     * @param $linkTemplateName
     * @return $this
     */
    public function setLinkTemplateName($linkTemplateName)
    {
        $this->linkTemplateName = $linkTemplateName;
        return $this;
    }


    public function renderLink($pageNumber, $title = null)
    {
        $title = $title ?: (string)$pageNumber;
        return $this->getRenderer()->render($this->linkTemplateName, [
            'isCurrent' => $this->requireDataItem('current') == $pageNumber,
            'url' => $this->makeUrl($pageNumber),
            'title' => $title ?: (string)$pageNumber
        ]);
    }

    /**
     * @param int $from
     * @param int $to
     * @return string
     */
    public function renderLinksRange($from, $to)
    {
        $out = '';
        for ($pageNumber = $from; $pageNumber <= $to; $pageNumber++) {
            $out .= $this->renderLink($pageNumber);
        }
        return $out;
    }

    /**
     * @param int|string $pageNumber
     * @return string
     */
    protected function makeUrl($pageNumber)
    {
        $urlKey = $this->requireDataItem('uriKey');
        return UriFunctions::replaceFragment(
            UriFunctions::modifyQuery(null, [$urlKey => $pageNumber]),
            ''
        );
    }
}
