<?php

namespace Tests\Acceptance;

use PHPUnit_Framework_TestCase;

/**
 * Class TestCase
 *
 * @package Tests\Acceptance
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var Client */
    private $client;

    /**
     * @return Client
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
    }
}