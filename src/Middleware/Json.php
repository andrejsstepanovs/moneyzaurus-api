<?php

namespace Api\Middleware;

use Slim\Middleware;

/**
 * Class Json
 *
 * @package Api\Middleware
 */
class Json extends Middleware
{
    /**
     * Setup json response
     */
    public function call()
    {
        /** @var \Api\Slim $app */
        $app = $this->getApplication();

        $this->getNextMiddleware()->call();

        $response = $app->response();
        $response->headers()->set('Content-Type', 'application/json; charset=utf-8');
        $response->setBody(json_encode($app->getData(), JSON_UNESCAPED_UNICODE));
    }
}