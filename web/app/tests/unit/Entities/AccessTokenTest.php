<?php

namespace Tests\Entities;

use Api\Entities\AccessToken;
use Tests\TestCase;

/**
 * Class AccessTokenTest
 *
 * @package Tests
 */
class AccessTokenTest extends TestCase
{
    /** @var AccessToken */
    private $sut;

    public function setUp()
    {
        $this->sut = new AccessToken();
    }

    public function testGetId()
    {
        $value = 123;
        $response = $this->sut->setId($value)->getId();

        $this->assertEquals($value, $response);
    }

    public function testGetCreated()
    {
        $value = new \DateTime();
        $response = $this->sut->setCreated($value)->getCreated();

        $this->assertEquals($value, $response);
    }

    public function testUsedAt()
    {
        $value = new \DateTime();
        $response = $this->sut->setUsedAt($value)->getUsedAt();

        $this->assertEquals($value, $response);
    }

    public function testValidUntil()
    {
        $value = new \DateTime();
        $response = $this->sut->setValidUntil($value)->getValidUntil();

        $this->assertEquals($value, $response);
    }

    public function testGetUser()
    {
        $value = $this->mock()->get('Api\Entities\User');
        $response = $this->sut->setUser($value)->getUser();

        $this->assertEquals($value, $response);
    }

    public function testGetToken()
    {
        $value = 'token';
        $response = $this->sut->setToken($value)->getToken();

        $this->assertEquals($value, $response);
    }
}
