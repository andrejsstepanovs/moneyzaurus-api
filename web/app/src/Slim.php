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

    public function getRequestValue($key)
    {
        $request = parent::request();

        $value = $request->post($key);
        if (!empty($value)) {
            return $value;
        }

        $value = $request->get($key);
        if (!empty($value)) {
            return $value;
        }

        $value = $request->getBody();
        if (!empty($value)) {
            $data = json_decode($value, true);
            if (array_key_exists($key, $data)) {
                return $data[$key];
            }
        }

        return null;
    }
}
