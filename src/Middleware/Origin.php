<?php

namespace Api\Middleware;

use Slim\Middleware;
use Slim\Http\Response;
use Slim\Http\Request;
use Api\Slim;

/**
 * Class Origin
 *
 * sets response headers
 *
 * @package Api\Middleware
 */
class Origin extends Middleware
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
        $origin = $app->request()->headers('Origin');

        if ($origin) {
            $heders = $response->headers();
            $heders->set('Access-Control-Allow-Origin', $origin);
            $heders->set('Access-Control-Allow-Headers', 'X-Requested-With');
            $heders->set('Access-Control-Allow-Headers', 'Content-Type');
            $heders->set('Access-Control-Max-Age', '86400');

            $allowMethods = [
                Request::METHOD_GET,
                Request::METHOD_POST,
                Request::METHOD_DELETE,
                Request::METHOD_PUT,
                Request::METHOD_OPTIONS
            ];
            $heders->set('Access-Control-Allow-Methods', implode(', ', $allowMethods));
        }
    }
}