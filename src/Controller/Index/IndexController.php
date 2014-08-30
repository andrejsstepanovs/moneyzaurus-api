<?php

namespace Api\Controller\Index;

/**
 * Class IndexController
 *
 * @package Api\Controller\Index
 */
class IndexController
{
    /**
     * @return array
     */
    public function getResponse()
    {
        $data = array(
            'version' => 'V1'
        );

        return $data;
    }
}
