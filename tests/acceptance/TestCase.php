<?php

namespace Tests\Acceptance;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Message\Response;

/**
 * Class TestCase
 *
 * @package Tests\Acceptance
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var HttpClient */
    private $client;

    /** @var string */
    private $baseUrl;

    /**
     * @return string
     */
    private function getBaseUrl()
    {
        if ($this->baseUrl === null) {
            $this->baseUrl = 'http://' . WEB_SERVER_HOST . ':' . WEB_SERVER_PORT;
        }

        return $this->baseUrl;
    }

    /**
     * @return HttpClient
     */
    public function getClient()
    {
        if (null === $this->client) {
            $this->client = new HttpClient();
        }

        return $this->client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $params
     *
     * @return Response
     */
    public function call($method, $url, array $params = array())
    {
        $client = $this->getClient();

        $request = $client->createRequest($method);
        $request->setUrl($this->getBaseUrl() . $url);
        $request->setQuery(http_build_query($params));

        /** @var Response $response */
        $response = $client->send($request);

        return $response;
    }
}