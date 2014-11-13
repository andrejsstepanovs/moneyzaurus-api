<?php

namespace Api\Middleware;

use Slim\Middleware;
use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Http\Headers as SlimHeaders;
use Api\Slim;

/**
 * Class Origin
 *
 * sets response headers
 *
 * @package Api\Middleware
 */
class Headers extends Middleware
{
    /**
     * Setup json response
     */
    public function call()
    {
        /** @var Slim $app */
        $app = $this->getApplication();

        $response = $app->response();

        $continue = $this->modifyResponse($app, $response);
        if ($continue) {
            $this->getNextMiddleware()->call();
        }
    }

    /**
     * @param Slim     $app
     * @param Response $response
     *
     * @response bool continue
     */
    public function modifyResponse(Slim $app, Response $response)
    {
        $headers = $response->headers();
        $request = $app->request();

        $this->handleOriginHeader($request, $headers);
        $this->handleGlobalHeaders($headers);

        if ($request->getMethod() == Request::METHOD_OPTIONS) {
            $data = ['options' => true];
            $app->setData($data);

            $response = $app->response();
            $response->setStatus(200);

            return false;
        }

        return true;
    }

    /**
     * @param Request     $request
     * @param SlimHeaders $headers
     *
     * @return $this
     */
    private function handleOriginHeader(Request $request, SlimHeaders $headers)
    {
        $origin = $request->headers('Origin');
        if ($origin) {
            $headers->set('Access-Control-Allow-Origin', $origin);
        }

        return $this;
    }

    /**
     * @param SlimHeaders $headers
     *
     * @return $this
     */
    private function handleGlobalHeaders(SlimHeaders $headers)
    {
        $headers->set('Access-Control-Allow-Headers', 'X-Requested-With');
        $headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $headers->set('Access-Control-Max-Age', '86400');

        $allowMethods = [
            Request::METHOD_GET,
            Request::METHOD_POST,
            Request::METHOD_DELETE,
            Request::METHOD_PUT,
            Request::METHOD_OPTIONS,
        ];
        $headers->set('Access-Control-Allow-Methods', implode(', ', $allowMethods));

        return $this;
    }
}
