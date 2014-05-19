<?php

namespace Tests\Entities;


use Api\Entities\Currency;
use Tests\TestCase;

/**
 * Class CurrencyTest
 *
 * @package Tests
 */
class CurrencyTest extends TestCase
{
    /** @var Currency */
    private $sut;

    public function setUp()
    {
        $this->sut = new Currency();
    }

    public function testGetCurrency()
    {
        $value = 'EUR';
        $response = $this->sut->setCurrency($value)->getCurrency();

        $this->assertEquals($value, $response);
    }

    public function testGetDateCreated()
    {
        $value = new \DateTime();
        $response = $this->sut->setDateCreated($value)->getDateCreated();

        $this->assertEquals($value, $response);
    }

    public function testGetHtml()
    {
        $value = '€';
        $response = $this->sut->setHtml($value)->getHtml();

        $this->assertEquals($value, $response);
    }

    public function testGetName()
    {
        $value = 'name';
        $response = $this->sut->setName($value)->getName();

        $this->assertEquals($value, $response);
    }

}