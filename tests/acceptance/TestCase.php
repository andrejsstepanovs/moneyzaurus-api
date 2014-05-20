<?php

namespace Tests\Acceptance;

use PHPUnit_Framework_TestCase;
use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Message\Response;

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
    protected function getBaseUrl()
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
     * @param string $url
     * @param array  $postBody
     *
     * @return Response
     */
    public function post($url, array $postBody = array())
    {
        $requestUrl = $this->getBaseUrl() . $url;
        $request = $this->getClient()->post($requestUrl, null, $postBody);
        $response = $this->getClient()->send($request);

        return $response;
    }

    /**
     * @param string $url
     *
     * @return Response
     */
    public function get($url)
    {
        $requestUrl = $this->getBaseUrl() . $url;
        $request = $this->getClient()->get($requestUrl);
        $response = $this->getClient()->send($request);

        return $response;
    }

}