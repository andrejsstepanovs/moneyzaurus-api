<?php

namespace Tests\Service\Transaction;

use Api\Service\Transaction\Money;
use Tests\TestCase;

/**
 * Class MoneyTest
 *
 * @package Tests\Service\Transaction
 */
class MoneyTest extends TestCase
{
    /** @var Money */
    private $sut;

    public function setUp()
    {
        $this->sut = new Money();
    }

    /**
     * @return array
     */
    public function getAmountDataProvider()
    {
        return array(
            array('1', 100),
            array('10', 1000),
            array('10.00', 1000),
            array('10.1234', 1012),
            array('0.99', 99),
            array('0.6', 60),
            array('0.01', 1),
            array('.01', 1),
            array('7.01EUR', 701), // @todo not allowed
            array('EUR7.01', 0),   // @todo not allowed
        );
    }

    /**
     * @dataProvider getAmountDataProvider
     *
     * @param string $price
     * @param int    $expected
     */
    public function testGetAmount($price, $expected)
    {
        $result = $this->sut->getAmount($price);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getPriceDataProvider()
    {
        return array(
            array(100, '1.00'),
            array(1000, '10.00'),
            array(123, '1.23'),
            array(1, '0.01'),
            array(0, '0.00'),
            array('98', '0.98'),
            array('9801', '98.01'),
            array('', '0.00'),
        );
    }

    /**
     * @dataProvider getPriceDataProvider
     *
     * @param string $amount
     * @param int    $expected
     */
    public function testGetPrice($amount, $expected)
    {
        $result = $this->sut->getPrice($amount);

        $this->assertEquals($expected, $result);
    }

}