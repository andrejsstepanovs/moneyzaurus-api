<?php

namespace Api\Middleware;

use Slim\Middleware;
use Slim\Http\Response;
use Api\Slim;
use Api\Service\Json as JsonService;
use Api\Service\AccessorTrait;

/**
 * Class Json
 *
 * @package Api\Middleware
 *
 * @method Authorization setJsonService(JsonService $jsonService)
 * @method JsonService   getJsonService()
 */
class Json extends Middleware
{
    use AccessorTrait;

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

        $jsonService = $this->getJsonService();
        $data        = $app->getData();
        $json        = $jsonService->encode($data);

        if ($json !== false) {
            $response->setBody($json);
        } else {
            // @todo add correct error code
            $response->setBody('Failed to parse to json. Error: ' . $jsonService->getJsonErrorMessage());
        }
    }
}
