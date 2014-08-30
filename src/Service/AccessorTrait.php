<?php

namespace Api\Service;

use Pimple;

/**
 * Class AccessorTrait
 *
 * @package Api\Service
 */
trait AccessorTrait
{
    /** @var Pimple */
    private $pimple;

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function __call($name, $arguments)
    {
        $type  = substr($name, 0, 3);
        $id    = substr($name, 3);

        switch ($type) {
            case 'set':
                $value = $this->value($arguments);
                $this->pimple()->offsetSet($id, $value);
                break;
            case 'get':
                return $this->pimple()->offsetGet($id);
                break;
            default:
                throw new \RuntimeException('Type "' . $type . '" not found in "' . $name . '"');
                break;
        }

        return $this;
    }

    /**
     * @return Pimple
     */
    private function pimple()
    {
        if (is_null($this->pimple)) {
            $this->pimple = new Pimple();
        }

        return $this->pimple;
    }

    /**
     * @param array $arguments
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function value(array $arguments)
    {
        if (array_key_exists(0, $arguments)) {
            return $arguments[0];
        }

        throw new \InvalidArgumentException('Argument not found');
    }

}
