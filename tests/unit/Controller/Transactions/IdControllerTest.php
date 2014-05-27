<?php

namespace Tests\Controller\Transactions;

use Api\Controller\Transactions\IdController;
use Tests\TestCase;

/**
 * Class CreateControllerTest
 *
 * @package Tests\Controller\Transactions
 */
class IdControllerTest extends TestCase
{
    /** @var IdController */
    private $sut;

    public function setUp()
    {
        $this->sut = new IdController();
        $this->sut->setMoney($this->mock()->get('Api\Service\Transaction\Money'));
        $this->sut->setData($this->mock()->get('Api\Service\Transaction\Data'));
        $this->sut->setValidate($this->mock()->get('Api\Service\Transaction\Validate'));

        $this->mock()->get('Api\Entities\Transaction')
            ->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->mock()->get('Api\Entities\User')));
    }

    public function testTransactionIdNotFound()
    {
        $connectedUserIds = array();
        $transactionId = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo($transactionId))
            ->will($this->returnValue(null));

        $response = $this->sut->getResponse(
            $this->mock()->get('Api\Entities\User'),
            $connectedUserIds,
            $transactionId
        );

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('success', $response);
        $this->assertFalse($response['success']);
    }

    public function testTransactionFoundButNotAllowed()
    {
        $connectedUserIds = array();
        $transactionId = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
            ->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $transactionId);

        $this->assertFalse($response['success']);
    }

    public function testSuccessfulId()
    {
        $connectedUserIds = array();
        $transactionId = 1;

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('find')
             ->will($this->returnValue($this->mock()->get('Api\Entities\Transaction')));

        $this->mock()->get('Api\Service\Transaction\Validate')
             ->expects($this->once())
             ->method('isAllowed')
             ->will($this->returnValue(true));

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('toArray')
             ->will($this->returnValue(array()));

        $this->mock()->get('Api\Service\Transaction\Data')
             ->expects($this->once())
             ->method('normalizeResults')
             ->will($this->returnValue(array(array('apple' => 'red'))));


        $response = $this->sut->getResponse($this->mock()->get('Api\Entities\User'), $connectedUserIds, $transactionId);

        $this->assertTrue($response['success']);
        $this->assertEquals(
            array(
                'success' => true,
                'data' => array(
                    'apple' => 'red'
                )
            ),
            $response
        );
    }

}
