<?php

namespace Tests\Controller\Chart;

use Api\Controller\Chart\PieController;
use Tests\TestCase;

/**
 * Class PieControllerTest
 *
 * @package Tests\Controller\Transactions
 */
class PieControllerTest extends TestCase
{
    /** @var PieController */
    private $sut;

    public function setUp()
    {
        $this->sut = new PieController();
        $this->sut->setChartPie($this->mock()->get('Api\Service\Chart\Pie'));
        $this->sut->setDate($this->mock()->get('Api\Service\Transaction\Date'));
    }

    public function testGetPieChartWithoutCurrency()
    {
        $connectedUserIds = array();
        $currency = '';
        $from     = null;
        $till     = null;

        $response = $this->sut->getResponse(
            $this->mock()->get('Api\Entities\User'),
            $connectedUserIds,
            $currency,
            $from,
            $till
        );

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(0, $response['count']);
        $this->assertFalse($response['success']);
        $this->assertNotEmpty($response['message']);
    }

    public function testGetPieChartData()
    {
        $connectedUserIds = array();
        $currency = 'EUR';
        $from     = '2013-12-30';
        $till     = '2014-12-30';
        $dateTime = new \DateTime();

        $data = array(
            array(
                'amount'    => 100,
                'groupId'   => 1,
                'groupName' => 'group',
            ),
        );

        $normalizedData = array(
            array(
                'amount'    => 100,
                'groupId'   => 1,
                'groupName' => 'group',
                'price'     => 1.00,
                'money'     => '€1.00',
            ),
        );

        $this->mock()->get('Api\Service\Chart\Pie')
             ->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));

        $this->mock()->get('Api\Service\Chart\Pie')
             ->expects($this->once())
             ->method('normalizeResults')
             ->will($this->returnValue($normalizedData));

        $this->mock()->get('Api\Service\Transaction\Date')
             ->expects($this->any())
             ->method('getDateTime')
             ->will($this->returnValue($dateTime));

        $response = $this->sut->getResponse(
            $this->mock()->get('Api\Entities\User'),
            $connectedUserIds,
            $currency,
            $from,
            $till
        );

        $this->assertTrue($response['success']);
        $this->assertEquals(1, $response['count']);
        $this->assertEquals($normalizedData, $response['data']);
        $this->assertEquals($currency, $response['currency']);
        $this->assertNotEmpty($response['from']);
        $this->assertNotEmpty($response['till']);
    }
}
