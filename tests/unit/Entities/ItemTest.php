<?php

namespace Tests\Entities;

use Api\Entities\Item;
use Tests\TestCase;

/**
 * Class ItemTest
 *
 * @package Tests
 */
class ItemTest extends TestCase
{
    /** @var Item */
    private $sut;

    public function setUp()
    {
        $this->sut = new Item();
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

    public function testGetName()
    {
        $value = 'name';
        $response = $this->sut->setName($value)->getName();

        $this->assertEquals($value, $response);
    }
}
