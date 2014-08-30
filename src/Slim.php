<?php

namespace Api;

use Slim\Slim as SlimApp;

/**
 * Class Slim
 *
 * @package Api
 */
class Slim extends SlimApp
{
    /** @var array */
    public $data;

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
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
}
