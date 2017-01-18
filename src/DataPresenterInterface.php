<?php

namespace ViewComponents\Core;

interface DataPresenterInterface extends BlockInterface
{
    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param $data
     * @return $this
     */
    public function setData($data);
}
