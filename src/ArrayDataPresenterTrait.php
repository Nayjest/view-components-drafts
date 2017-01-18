<?php

namespace ViewComponents\Core;

use Exception;

trait ArrayDataPresenterTrait
{
    private $data = [];

    /**
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function setData($data)
    {
        if (!is_array($data)) {
            throw new Exception("Trying to set non-array data");
        }
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function getDataItem($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    public function requireDataItem($key)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new Exception("Missing required data item: $key");
        }
        return $this->data[$key];
    }

    public function setDataItem($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function mergeData(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
}
