<?php

namespace ViewComponents\Core\Block\ListBlock\Pagination;


use ViewComponents\Core\BlockInterface;

interface PaginationViewInterface extends BlockInterface
{
    /**
     * @param int $value
     * @return $this
     */
    public function setCurrent($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setTotal($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setUriKey($value);
}