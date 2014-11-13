<?php

namespace Tests\Entities;

use Api\Entities\Transaction;
use Tests\TestCase;

/**
 * Class TransactionTest
 *
 * @package Tests
 */
class TransactionTest extends TestCase
{
    /** @var Transaction */
    private $sut;

    public function setUp()
    {
        $this->sut = new Transaction();
    }

    public function testGetId()
    {
        $value = 123;
        $response = $this->sut->setId($value)->getId();

        $this->assertEquals($value, $response);
    }

    public function testGetDate()
    {
        $value = new \DateTime();
        $response = $this->sut->setDate($value)->getDate();

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

    public function testGetCurrency()
    {
        $value = $this->mock()->get('Api\Entities\Currency');
        $response = $this->sut->setCurrency($value)->getCurrency();

        $this->assertEquals($value, $response);
    }

    public function testGetGroup()
    {
        $value = $this->mock()->get('Api\Entities\Group');
        $response = $this->sut->setGroup($value)->getGroup();

        $this->assertEquals($value, $response);
    }

    public function testGetItem()
    {
        $value = $this->mock()->get('Api\Entities\Item');
        $response = $this->sut->setItem($value)->getItem();

        $this->assertEquals($value, $response);
    }

    public function testGetPrice()
    {
        $value = 12345;
        $response = $this->sut->setPrice($value)->getPrice();

        $this->assertEquals($value, $response);
    }
}
