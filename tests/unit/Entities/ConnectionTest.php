<?php

namespace Tests\Entities;

use Api\Entities\Connection;
use Tests\TestCase;

/**
 * Class ConnectionTest
 *
 * @package Tests
 */
class ConnectionTest extends TestCase
{
    /** @var Connection */
    private $sut;

    public function setUp()
    {
        $this->sut = new Connection();
    }

    public function testGetId()
    {
        $value = 123;
        $response = $this->sut->setId($value)->getId();

        $this->assertEquals($value, $response);
    }

    public function testGetDateCreated()
    {
        $value = new \DateTime();
        $response = $this->sut->setDateCreated($value)->getDateCreated();

        $this->assertEquals($value, $response);
    }

    public function testGetUser()
    {
        $value = $this->mock()->get('Api\Entities\User');
        $response = $this->sut->setUser($value)->getUser();

        $this->assertEquals($value, $response);
    }

    public function testGetParent()
    {
        $value = $this->mock()->get('Api\Entities\User');
        $response = $this->sut->setParent($value)->getParent();

        $this->assertEquals($value, $response);
    }

    public function testGetState()
    {
        $value = 'state';
        $response = $this->sut->setState($value)->getState();

        $this->assertEquals($value, $response);
    }
}
