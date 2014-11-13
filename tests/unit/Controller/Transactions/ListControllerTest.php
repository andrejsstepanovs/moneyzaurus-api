<?php

namespace Tests\Controller\Transactions;

use Api\Controller\Transactions\ListController;
use Tests\TestCase;

/**
 * Class ListControllerTest
 *
 * @package Tests\Controller\Transactions
 */
class ListControllerTest extends TestCase
{
    /** @var ListController */
    private $sut;

    public function setUp()
    {
        $this->sut = new ListController();
        $this->sut->setData($this->mock()->get('Api\Service\Transaction\Data'));
        $this->sut->setDate($this->mock()->get('Api\Service\Transaction\Date'));
    }

    public function testGet()
    {
        $connectedUserIds = array();
        $offset = 1;
        $limit = 1;
        $from = '2013-12-30';
        $till = '2014-12-30';
        $item = 'item';
        $group = 'group';
        $price = '10.00';
        $data  = array('apple');

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('getTransactionsList')
             ->will($this->returnValue(array()));

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('normalizeResults')
             ->will($this->returnValue($data));

        $response = $this->sut->getResponse(
            $this->mock()->get('Api\Entities\User'),
            $connectedUserIds,
            $offset,
            $limit,
            $from,
            $till,
            $item,
            $group,
            $price
        );

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertArrayHasKey('count', $response);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(0, $response['count']);
        $this->assertEquals($data, $response['data']);
    }
}
