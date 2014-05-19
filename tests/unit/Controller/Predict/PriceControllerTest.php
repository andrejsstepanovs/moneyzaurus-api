<?php

namespace Tests\Controller\Login;

use Api\Controller\Predict\PriceController;
use Tests\TestCase;

/**
 * Class PriceControllerTest
 *
 * @package Tests
 */
class PriceControllerTest extends TestCase
{
    /** @var PriceController */
    private $sut;

    public function setUp()
    {
        $this->sut = new PriceController();
        $this->sut->setPredictPrice($this->mock()->get('Api\Service\Predict\Price'));
        $this->sut->setData($this->mock()->get('Api\Service\Transaction\Data'));
    }

    public function dataProvider()
    {
        return array(
            array(
                array(),
                array(
                    'success' => true,
                    'count'   => 0,
                    'data'    => array(),
                )
            ),
            array(
                array('test'),
                array(
                    'success' => true,
                    'count'   => 1,
                    'data'    => array('test'),
                )
            ),
            array(
                array('apple', 'banana'),
                array(
                    'success' => true,
                    'count'   => 2,
                    'data'    => array('apple', 'banana'),
                )
            ),
        );
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $prices
     * @param array $expected
     */
    public function testWillReturnExpected(array $prices, array $expected)
    {
        $connectedUserIds = array();
        $item  = 'item';
        $group = 'group';

        $this->mock()->get('Api\Service\Predict\Price')
             ->expects($this->once())
             ->method('predict')
             ->will($this->returnValue($prices));

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('normalizeResults')
             ->with($this->equalTo($prices))
             ->will($this->returnValue($prices));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $item, $group);

        $this->assertEquals($expected, $response);
    }

}