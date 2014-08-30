<?php

namespace Api\Middleware;

use Slim\Middleware;
use Slim\Http\Response;
use Api\Slim;

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
        /** @var Slim $app */
        $app = $this->getApplication();

        $this->getNextMiddleware()->call();

        $response = $app->response();

        $this->modifyResponse($app, $response);
    }

    /**
     * @param Slim     $app
     * @param Response $response
     */
    public function modifyResponse(Slim $app, Response $response)
    {
        $response->headers()->set('Content-Type', 'application/json; charset=utf-8');
        $response->setBody(json_encode($app->getData(), JSON_UNESCAPED_UNICODE));
    }
}
